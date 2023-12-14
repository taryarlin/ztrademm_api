<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_key')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('product_id');
            $table->bigInteger('quantity')->default(1)->nullable();
            $table->bigInteger('single_price')->default(1)->nullable();
            $table->bigInteger('total_price')->default(1)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_sessions');
    }
}
