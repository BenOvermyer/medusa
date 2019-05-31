<?php

use Illuminate\Database\Migrations\Migration;

class AddStaffMemberBillet extends Migration
{
    use App\Traits\MedusaAudit;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->writeAuditTrail(
            'system user',
            'create',
            'billets',
            null,
            json_encode(['billet_name' => 'Staff']),
            'add_flag_lt'
        );
        App\Billet::create(['billet_name' => 'Staff']);
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
