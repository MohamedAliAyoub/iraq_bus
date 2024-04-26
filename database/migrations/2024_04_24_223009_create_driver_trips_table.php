<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_trips', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger("booked_count")->default(0);
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId("trip_id");
            $table->date('date')->nullable();
            $table->tinyInteger("status")->default(0)->comment("0=>pending , 1=>driver accept , 2=> driver cancel");

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
        Schema::dropIfExists('driver_trips');
    }
}
