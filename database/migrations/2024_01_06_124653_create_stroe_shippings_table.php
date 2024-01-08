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
        Schema::create('stroe_shippings', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id')->default(0);
            $table->integer('shipping_method')->default(0);
            $table->float('shipping_price')->default(0);
            $table->integer('is_default')->default(0);
            $table->integer('is_free')->default(0);
            $table->integer('is_active')->default(0);            
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
        Schema::dropIfExists('stroe_shippings');
    }
};
