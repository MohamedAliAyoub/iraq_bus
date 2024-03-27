<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignedVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigned_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('trip_id')->default(0);
            $table->unsignedInteger('vehicle_id')->default(0);
            $table->time('start_from')->nullable();
            $table->time('end_at')->nullable();
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
        Schema::dropIfExists('assigned_vehicles');
    }
}
