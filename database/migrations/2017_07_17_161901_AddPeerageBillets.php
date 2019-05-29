<?php

use App\Models\Billet;
use App\Models\Audit\MedusaAudit;
use Illuminate\Database\Migrations\Migration;

class AddPeerageBillets extends Migration
{
    use MedusaAudit;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ([
                     'Knight',
                     'Dame',
                     'Baron',
                     'Baroness',
                     'Earl',
                     'Countess',
                     'Duke',
                     'Duchess',
                     'Grand Duke',
                     'Grand Duchess',
                     'Steadholder',
                     'Majordomo',
                 ] as $billet) {
            try {
                App\Models\Billet::create(['billet_name' => $billet]);

                $this->writeAuditTrail(
                    'migration',
                    'create',
                    'billet',
                    null,
                    json_encode(['billet_name' => $billet]),
                    'AddPeerageBillets Migration'
                );
            } catch (Exception $e) {
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ([
                     'Knight',
                     'Dame',
                     'Baron',
                     'Baroness',
                     'Earl',
                     'Countess',
                     'Duke',
                     'Duchess',
                     'Grand Duke',
                     'Grand Duchess',
                     'Steadholder',
                     'Majordomo',
                 ] as $billet) {
            try {
                $billetId = App\Models\Billet::where('billet_name', $billet)->first()->id;

                App\Models\Billet::destroy($billetId);

                $this->writeAuditTrail(
                    'migration',
                    'destroy',
                    'billet',
                    $billetId,
                    null,
                    'AddPeerageBillets Migration'
                );
            } catch (Exception $e) {
            }
        }
    }
}
