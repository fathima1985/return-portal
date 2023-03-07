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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->text('order_id');
            $table->text('payment_id')->nullable();
            $table->text('order_email')->nullable();
            $table->string('order_site')->nullable();
            $table->string('site_code')->nullable();
            $table->date('order_date')->nullable();            
            $table->text('lang')->nullable();
            $table->text('expected_pickup')->nullable();
            $table->text('shipment_status')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('shipments');
    }
};
