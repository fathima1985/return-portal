<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id');
            $table->date('collection_date');
            $table->text('name')->nullable();
            $table->text('street')->nullable();
            $table->text('house_no')->nullable();
            $table->text('city')->nullable();
            $table->text('country')->nullable();
            $table->text('post_code')->nullable();
            $table->text('phone_no')->nullable();
            $table->text('extension')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('shipment_addresses');
    }
};
