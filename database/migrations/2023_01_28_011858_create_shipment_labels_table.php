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
        Schema::create('shipment_labels', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id');
            $table->text('TrackingCode');
            $table->text('label_pdf');
            $table->text('TrackingLink');
            $table->boolean('is_sent')->default();
            $table->boolean('is_link')->default();
            $table->string('label_info');
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
        Schema::dropIfExists('shipment_labels');
    }
};
