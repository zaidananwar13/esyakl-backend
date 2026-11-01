<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EventParticipantTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE OR REPLACE FUNCTION update_quota()
        RETURNS TRIGGER 
        LANGUAGE PLPGSQL
        AS
        $$
        BEGIN
            IF (TG_OP = 'DELETE') THEN
                UPDATE event SET quota = quota + 1 
                WHERE id_event = OLD.id_event;

                RETURN OLD;
            ELSIF (TG_OP = 'INSERT') THEN
                UPDATE event SET quota = quota - 1 
                WHERE id_event = NEW.id_event;

                RETURN NEW;
            END IF;
            RETURN NULL;
        END;
        $$");

        DB::unprepared("CREATE TRIGGER event_participant_trigger
            AFTER INSERT OR DELETE ON event_participant
            FOR EACH ROW
            EXECUTE PROCEDURE update_quota();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared("DROP TRIGGER event_participant_trigger ON event_participant IF EXISTS");
    }
}
