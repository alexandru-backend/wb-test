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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('income_id')->unique(); // Chave primária da API
            $table->string('number')->nullable();
            $table->date('date')->nullable();
            $table->date('last_change_date')->nullable();
            $table->date('date_close')->nullable();
            $table->string('warehouse_name')->nullable();
            $table->string('supplier_article')->nullable();
            $table->string('tech_size')->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->bigInteger('nm_id')->nullable();
            $table->string('status')->nullable(); // Campo comum em Incomes
            
            // Mantendo os teus campos por segurança (caso a API os envie)
            $table->string('g_number')->nullable();
            $table->boolean('is_supply')->default(false);
            $table->boolean('is_realization')->default(false);
            $table->decimal('promo_code_sum', 10, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
