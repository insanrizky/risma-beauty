<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('identifier')->nullable();
            $table->string('upline_identifier')->nullable();
            $table->string('contact')->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('address')->nullable();
            $table->integer('bank_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ktp_file')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('shopee_link')->nullable();
            $table->string('payment_file')->nullable();
            $table->string('status');
            $table->integer('total_point')->nullable();
            $table->integer('total_amount')->nullable();
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
        Schema::dropIfExists('user_details');
    }
}
