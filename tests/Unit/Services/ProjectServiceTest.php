<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    protected ProjectService $service;
    protected string $testToken;
    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        Storage::fake('project');

        $this->service = new ProjectService();

        $this->testToken = hash('sha256', 'project-service-test-token');
        DB::table('bahasa')->insertOrIgnore(['id_bahasa' => 1, 'bahasa' => 'Bahasa Indonesia']);
        $userId = DB::table('user')->insertGetId([
            'name' => 'Project Test User',
            'email' => 'project.test@mail.test',
            'api_token' => $this->testToken,
            'type' => 'student',
        ], 'id_user');
        $this->testUser = User::find($userId);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_projects_returns_success_response()
    {
        $result = $this->service->getAllProjects();

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Project found.', $result['message']);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_get_project_by_class_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->getProjectByClass('invalid-token', 1);

        // Returns error when project not found or user unauthorized
        $this->assertFalse($result['success']);
        $this->assertContains($result['code'], [401, 404]);
    }

    public function test_get_project_by_class_returns_not_found_when_project_missing()
    {
        $result = $this->service->getProjectByClass('test-token', 99999);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('Project not found.', $result['message']);
    }

    public function test_submit_project_returns_unauthorized_when_user_not_found()
    {
        $result = $this->service->submitProject('invalid-token', 1, 'test note', null);

        $this->assertFalse($result['success']);
        $this->assertEquals(401, $result['code']);
        $this->assertEquals('Unauthorized', $result['message']);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new ProjectService();
        $this->assertInstanceOf(ProjectService::class, $service);
    }

    public function test_get_all_projects_returns_array()
    {
        $result = $this->service->getAllProjects();
        $this->assertIsArray($result);
    }

    public function test_get_project_by_class_returns_array()
    {
        $result = $this->service->getProjectByClass('token', 1);
        $this->assertIsArray($result);
    }

    public function test_submit_project_returns_array()
    {
        $result = $this->service->submitProject('token', 1, 'note', null);
        $this->assertIsArray($result);
    }

    public function test_submit_project_succeeds_when_lookup_by_id_project()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 6, 'Note by id_project', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Upload Project Successful', $result['message']);
        $this->assertDatabaseHas('project_student', [
            'id_user' => $this->testUser->id_user,
            'id_project' => 6,
        ]);
    }

    public function test_submit_project_succeeds_when_lookup_by_id_class()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 11, 'Note by id_class', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
        $this->assertEquals('Upload Project Successful', $result['message']);
        $this->assertDatabaseHas('project_student', [
            'id_user' => $this->testUser->id_user,
            'id_project' => 6,
        ]);
    }

    public function test_submit_project_accepts_all_pdf_variant_mime_types()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $mimes = [
            'application/pdf',
            'application/x-pdf',
            'application/acrobat',
            'applications/vnd.pdf',
        ];

        foreach ($mimes as $mime) {
            $file = UploadedFile::fake()->create('variant.pdf', 50, $mime);
            $result = $this->service->submitProject($this->testToken, 6, "Note for $mime", $file);
            $this->assertTrue($result['success'], "Failed for MIME: $mime");
            $this->assertEquals(200, $result['code']);
        }
    }

    public function test_submit_project_fails_with_disallowed_mime_type()
    {
        $file = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');
        $result = $this->service->submitProject($this->testToken, 6, 'Note', $file);

        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
        $this->assertStringContainsString('Invalid file type', $result['message']);
    }

    public function test_submit_project_fails_when_file_size_exceeds_max()
    {
        $file = UploadedFile::fake()->create('big.pdf', 13 * 1024, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 6, 'Note', $file);

        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['code']);
        $this->assertStringContainsString('File size exceeds', $result['message']);
    }

    public function test_submit_project_permits_submission_when_completed_at_set_even_if_expired()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->subDays(10)->timestamp,
            'completed_at' => Carbon::now()->subDays(5)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 6, 'Completed note', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
    }

    public function test_submit_project_fails_when_expired_and_not_completed()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->subDays(10)->timestamp,
            'completed_at' => null,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 6, 'Note', $file);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('Class enrollment not found or expired', $result['message']);
    }

    public function test_submit_project_fails_when_project_not_found()
    {
        $file = UploadedFile::fake()->create('submission.pdf', 100, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 999999, 'Note', $file);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
        $this->assertEquals('Project not found', $result['message']);
    }

    public function test_submit_project_resubmission_resets_poin_and_adds_result()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file1 = UploadedFile::fake()->create('first.pdf', 50, 'application/pdf');
        $this->service->submitProject($this->testToken, 6, 'First attempt', $file1);

        DB::table('project_student')
            ->where('id_user', $this->testUser->id_user)
            ->where('id_project', 6)
            ->update(['poin' => 90]);

        $file2 = UploadedFile::fake()->create('second.pdf', 50, 'application/pdf');
        $res = $this->service->submitProject($this->testToken, 6, 'Second attempt', $file2);

        $this->assertTrue($res['success']);

        $projUser = DB::table('project_student')
            ->where('id_user', $this->testUser->id_user)
            ->where('id_project', 6)
            ->first();

        $this->assertNull($projUser->poin);

        $count = DB::table('project_result')
            ->where('id_project_student', $projUser->id_project_student)
            ->count();
        $this->assertEquals(2, $count);
    }

    public function test_submit_project_accepts_rar_file_with_x_rar_mime()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.rar', 50, 'application/x-rar');
        $result = $this->service->submitProject($this->testToken, 6, 'RAR submission', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
    }

    public function test_submit_project_accepts_rar_file_with_application_rar_mime()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.rar', 50, 'application/rar');
        $result = $this->service->submitProject($this->testToken, 6, 'RAR submission', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
    }

    public function test_submit_project_resolves_id_collision_favoring_legacy_id_class_when_enrolled_in_both()
    {
        // Class 11 has Project 6. Class 35 has Project 11.
        // User enrolled in both Class 11 and Class 35.
        DB::table('class_student')->insert([
            [
                'id_user' => $this->testUser->id_user,
                'id_class' => 11,
                'expired_at' => Carbon::now()->addDays(30)->timestamp,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ],
            [
                'id_user' => $this->testUser->id_user,
                'id_class' => 35,
                'expired_at' => Carbon::now()->addDays(30)->timestamp,
                'created_at' => Carbon::now()->timestamp,
                'updated_at' => Carbon::now()->timestamp,
            ],
        ]);

        // When legacy client submits project=11 (meaning id_class 11),
        // it must submit to Project 6 (Class 11), not Project 11 (Class 35).
        $file = UploadedFile::fake()->create('submission.pdf', 50, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 11, 'Enrolled in both, pass 11', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
        $this->assertDatabaseHas('project_student', [
            'id_user' => $this->testUser->id_user,
            'id_project' => 6,
        ]);
    }

    public function test_submit_project_resolves_id_project_when_enrolled_only_in_target_class()
    {
        // User is enrolled in Class 35 (which has Project 11).
        // User is NOT enrolled in Class 11.
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 35,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        // Submitting project=11 resolves to Project 11 (Class 35) because user is enrolled in 35.
        $file = UploadedFile::fake()->create('submission.pdf', 50, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 11, 'Enrolled only in 35, pass id_project 11', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);
        $this->assertDatabaseHas('project_student', [
            'id_user' => $this->testUser->id_user,
            'id_project' => 11,
        ]);
    }

    public function test_submit_project_fails_when_completed_at_is_zero_and_expired()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->subDays(10)->timestamp,
            'completed_at' => 0,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $file = UploadedFile::fake()->create('submission.pdf', 50, 'application/pdf');
        $result = $this->service->submitProject($this->testToken, 6, 'Zero completed_at', $file);

        $this->assertFalse($result['success']);
        $this->assertEquals(404, $result['code']);
    }

    public function test_submit_project_accepts_file_when_client_mime_type_is_allowed()
    {
        DB::table('class_student')->insert([
            'id_user' => $this->testUser->id_user,
            'id_class' => 11,
            'expired_at' => Carbon::now()->addDays(30)->timestamp,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ]);

        $temp = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($temp, 'sample raw content');
        $file = new UploadedFile($temp, 'document.pdf', 'applications/vnd.pdf', null, true);

        $result = $this->service->submitProject($this->testToken, 6, 'Client MIME test', $file);

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['code']);

        @unlink($temp);
    }
}

