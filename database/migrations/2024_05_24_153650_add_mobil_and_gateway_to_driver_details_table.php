<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobilAndGatewayToDriverDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_details', function (Blueprint $table) {
            $table->string('mobile')->nullable();
            $table->foreignId('gateway')->constrained('gateways')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_details', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'gateway']);
        });
    }
}
