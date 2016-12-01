<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeputySpacelordBillet extends Migration
{
    use \Medusa\Audit\MedusaAudit;

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
            json_encode(["billet_name" => "Deputy Space Lord"]),
            'add_deputy_space_lord'
        );
        Billet::create(["billet_name" => "Deputy Space Lord"]);

        $this->writeAuditTrail(
            'system user',
            'create',
            'billets',
            null,
            json_encode(["billet_name" => "Space Lord"]),
            'add_deputy_space_lord'
        );
        Billet::create(["billet_name" => "Space Lord"]);
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
