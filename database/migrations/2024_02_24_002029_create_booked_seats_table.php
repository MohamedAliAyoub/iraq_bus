<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookedSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booked_ticket_id');
            $table->unsignedInteger('seat_id')->nullable();
            $table->string('client_name');
            $table->string('client_phone');
            $table->unsignedInteger('gender')->default(0);
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
        Schema::dropIfExists('booked_seats');
    }
}
