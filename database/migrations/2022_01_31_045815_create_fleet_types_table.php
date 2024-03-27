<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFleetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fleet_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('seat_layout', 40)->nullable();
            $table->unsignedInteger('deck')->default(0);
            $table->string('deck_seats', 40)->nullable();
            $table->string('facilities', 255)->nullable();
            $table->unsignedInteger('has_ac')->default(0);
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
        Schema::dropIfExists('fleet_types');
    }
}
