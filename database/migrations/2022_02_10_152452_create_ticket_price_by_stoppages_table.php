<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketPriceByStoppagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_price_by_stoppages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_price_id')->default(0);
            $table->string('source_destination', 50)->nullable();
            $table->double('price', 8,2)->default(0);
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
        Schema::dropIfExists('ticket_price_by_stoppages');
    }
}
