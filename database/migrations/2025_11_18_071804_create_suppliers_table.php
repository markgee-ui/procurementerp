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
        Schema::create('suppliers', function (Blueprint $table) {
            // Primary Key: Automatically increments and is the primary identifier.
            $table->id(); 

            // Supplier Fields
            $table->string('name', 255)->comment('Supplier Name')->index();
            $table->string('address', 500)->comment('Full physical address of the supplier');
            $table->string('location', 255)->comment('City/Country location for filtering');
            
            // Contact field handles either email or phone, keeping it flexible.
            $table->string('contact', 255)->comment('Supplier phone number or email address');
            
            // Laravel Timestamps: Adds 'created_at' and 'updated_at' columns.
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};