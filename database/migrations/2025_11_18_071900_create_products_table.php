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
        Schema::create('products', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key Link to Suppliers Table
            // Using foreignId is the standard, concise way in modern Laravel.
            $table->foreignId('supplier_id')
                  ->constrained() // Creates the foreign key constraint
                  ->onDelete('cascade'); // If a supplier is deleted, all their products are also deleted.
            
            // Product Details Fields
            $table->string('item', 255)->comment('Product item name');
            $table->text('description')->nullable()->comment('Detailed product specifications or description');
            
            // Price: Using decimal for precise currency storage (recommended over float/double).
            $table->decimal('unit_price', 10, 2)->comment('Unit price of the product');

            // Laravel Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};