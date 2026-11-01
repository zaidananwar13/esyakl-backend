<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectGradingTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE OR REPLACE FUNCTION update_project_res_status()
        RETURNS TRIGGER 
        LANGUAGE PLPGSQL
        AS
        $$
        BEGIN
            IF (TG_OP = 'UPDATE') THEN
                IF NEW.poin >= 80 THEN
                    UPDATE project_result SET status = 2, points = NEW.poin
                    WHERE id_project_result IN(
                        SELECT MAX(id_project_result) FROM project_result
                        WHERE id_project_student = NEW.id_project_student
                    );
                ELSE
                    UPDATE project_result SET status = 3, points = NEW.poin
                    WHERE id_project_result IN(
                        SELECT MAX(id_project_result) FROM project_result
                        WHERE id_project_student = NEW.id_project_student
                    );
                END IF;

                RETURN NEW;
            END IF;
            RETURN NULL;
        END;
        $$");

        DB::unprepared("CREATE TRIGGER project_grading_trigger
            AFTER UPDATE ON project_student
            FOR EACH ROW
            EXECUTE PROCEDURE update_project_res_status();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared("DROP TRIGGER project_grading_trigger ON project_student IF EXISTS");
    }
}
