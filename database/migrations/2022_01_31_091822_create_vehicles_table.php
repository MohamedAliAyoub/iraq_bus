<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('nick_name', 40)->nullable();
            $table->unsignedBigInteger('fleet_type_id')->default(0);
            $table->string('register_no', 255)->nullable();
            $table->string('engine_no', 255)->nullable();
            $table->string('chasis_no', 255)->nullable();
            $table->string('model_no', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
