<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            
            $table->bigInteger('nmId'); 
            $table->string('warehouseName')->nullable(); 
            $table->string('subject')->nullable();
            $table->string('brand')->nullable();
          
            $table->mediumInteger('quantity')->default(0);      
            $table->mediumInteger('inWayToClient')->default(0); 
            $table->mediumInteger('inWayFromClient')->default(0); 
            
            $table->string('category')->nullable(); 
            $table->string('price')->nullable(); 
            $table->mediumInteger('discount')->default(0);
            
            $table->date('download_date'); 

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
