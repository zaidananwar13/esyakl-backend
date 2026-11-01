<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PointRewardTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE OR REPLACE FUNCTION update_point_reward()
        RETURNS TRIGGER 
        LANGUAGE PLPGSQL
        AS
        $$
        BEGIN
            IF (TG_OP = 'UPDATE') THEN
                delete from point_student where
                id_class_student = NEW.id_class_student;

                insert into point_student(id_user, id_class_student, points, status, created_at, updated_at)
                values(NEW.id_user, NEW.id_class_student, NEW.poin, 0, extract(epoch from now()), extract(epoch from now()));
            END IF;
            RETURN NULL;
        END;
        $$");

        DB::unprepared("CREATE TRIGGER point_reward_trigger
            AFTER UPDATE ON project_student
            FOR EACH ROW
            EXECUTE PROCEDURE update_point_reward();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
