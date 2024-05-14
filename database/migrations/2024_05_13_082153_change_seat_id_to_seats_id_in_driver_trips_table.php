<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSeatIdToSeatsIdInDriverTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_trips', function (Blueprint $table) {
            $table->dropForeign(['seat_id']); // Drop foreign key constraint
            $table->dropColumn('seat_id'); // Drop the seat_id column

            $table->json('seats_id')->nullable(); // Add the seats_id column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_trips', function (Blueprint $table) {
            $table->dropColumn('seats_id'); // Drop the seats_id column

            $table->unsignedBigInteger('seat_id')->nullable(); // Add the seat_id column back
            $table->foreign('seat_id')->references('id')->on('seats')->onDelete('set null'); // Add foreign key constraint back
        });
    }
}