<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SillyAdminProfileTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::unprepared("CREATE OR REPLACE FUNCTION sillyAdminProfile()
        RETURNS TRIGGER 
        LANGUAGE PLPGSQL
        AS
        $$
        BEGIN
            IF (TG_OP = 'UPDATE') THEN
                UPDATE public.user SET avatar_original = NEW.avatar
                WHERE id_user = NEW.id_user;

                RETURN NEW;
            END IF;
            RETURN NULL;
        END;
        $$");

        DB::unprepared("CREATE TRIGGER sillyAdminProfileTrigger
            AFTER UPDATE ON public.user
            FOR EACH ROW
            EXECUTE PROCEDURE sillyAdminProfile();
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
