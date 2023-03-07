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
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id');
            $table->text('product_id');
            $table->text('product_sku');
            $table->text('product_title')->nullable();
            $table->text('line_price')->nullable();;
            $table->integer('quantity')->nullable();;
            $table->text('line_id')->nullable();;
            $table->text('total')->nullable();
            $table->text('total_tax')->nullable();
            $table->text('attributes')->nullable();
            $table->text('product_thumb')->nullable();
			$table->text('return_reason')->nullable();
            $table->text('hygiene_seal')->nullable();
            $table->text('is_opened')->nullable();
            $table->text('return_type')->nullable();
			$table->text('note')->nullable();
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
        Schema::dropIfExists('shipment_items');
    }
};
