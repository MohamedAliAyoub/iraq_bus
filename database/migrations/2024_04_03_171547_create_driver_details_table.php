<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string("first_id_card_image")->comment("صورة البطاقة وش");
            $table->string("second_id_card_image")->comment("صورة البطاقة ضهر");
            $table->string("first_residence_card_image")->comment("صورة بطاقة السكن وش");
            $table->string("last_residence_card_image")->comment("صورة بطاقة  السكن ضهر");
            $table->string("first_license_image")->comment("صورة الرخصة");
            $table->string("last_license_image")->comment("صورةالرخصة ضهر");
            $table->string("record")->nullable();
            $table->string("pdf")->nullable();
            $table->string("image")->nullable()->comment("صورة الموافقة من الادمن على السائق");
            $table->tinyInteger("status");


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
        Schema::dropIfExists('driver_details');
    }
}
