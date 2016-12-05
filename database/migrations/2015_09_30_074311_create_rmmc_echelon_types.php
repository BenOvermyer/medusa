<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRmmcEchelonTypes extends Migration
{

    use \Medusa\Audit\MedusaAudit;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add new RMMC Chapter types

        $this->createChapterType('corps', 'RMMC Corps');
        $this->createChapterType('exp_force', 'RMMC Expeditionary Force');
        $this->createChapterType('regiment', 'RMMC Regiment');
        $this->createChapterType('shuttle', 'RMMC Assault Shuttle');
        $this->createChapterType('section', 'RMMC Section');
        $this->createChapterType('squad', 'RMMC Squad');
        $this->createChapterType('platoon', 'RMMC Platoon');
        $this->createChapterType('company', 'RMMC Company');
        $this->createChapterType('battalion', 'RMMC Battalion');

        // Remove MARDET type

        $mardet = \Type::where('chapter_type', '=', 'mardet')->first();

        if (empty($mardet->id) === false) {
                   $this->writeAuditTrail(
            'system user',
            'delete',
            'types',
            $mardet->id,
            $mardet->toJson(),
            'create_rmmc_echelon_types'
        );

        $mardet->delete();
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

    function createChapterType($type, $description, array $can_have = [])
    {

        $this->writeAuditTrail(
            'system user',
            'create',
            'types',
            null,
            json_encode(['chapter_type' => $type, 'chapter_description' => $description, 'can_have' => $can_have]),
            'create_rmmc_echelon_types'
        );

        \Type::create(['chapter_type' => $type, 'chapter_description' => $description, 'can_have' => $can_have]);
    }

}
