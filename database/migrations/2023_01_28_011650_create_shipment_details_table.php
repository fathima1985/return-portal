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
        Schema::create('shipment_details', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id');
            $table->text('shiping_method');
            $table->text('shiping_price');
            $table->text('currency');
            $table->text('payment_method');
            $table->boolean('payment_status')->default(0);
            $table->text('payment_status_details')->nullable();
            $table->text('txn_id')->nullable();
            $table->text('store_note')->nullable();
            $table->boolean('mail_sent')->default(0);
            $table->string('customer_details')->nullable();
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
        Schema::dropIfExists('shipment_details');
    }
};
