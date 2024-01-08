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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->text('shipping_name')->unique();
            $table->text('shipping_title')->nullable();
            $table->text('shipping_logo')->nullable();            
            $table->integer('is_pickup')->default(0);
            $table->integer('status')->default(1);
            $table->integer('ship_label')->default(0);            
            $table->float('price')->default(0);
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
        Schema::dropIfExists('shipping_methods');
    }
};
