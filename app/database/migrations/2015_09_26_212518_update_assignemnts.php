<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssignemnts extends Migration
{

    use \Medusa\Audit\MedusaAudit;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $members = User::all();

        foreach ($members as $member) {
            $assignments = $member->assignment;
            foreach ($assignments as $key => $assignment) {
                if (empty( $assignment['primary'] ) === true) {
                    $assignment['secondary'] = true;
                    $assignments[$key] === $assignment;
                }
            }

            $this->writeAuditTrail(
                'system user',
                'update',
                'users',
                $member->id,
                $member->toJson(),
                'update assignemnts migration'
            );

            $member->save();
        }
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
