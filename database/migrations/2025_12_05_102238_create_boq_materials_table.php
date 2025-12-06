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
        Schema::create('boq_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_activity_id')->constrained('boq_activities')->onDelete('cascade');
            $table->string('item'); // e.g., Cement
            $table->string('specs')->nullable(); // e.g., 40kg
            $table->string('unit')->nullable(); // e.g., Bag, SqM
            $table->decimal('qty', 15, 2); // Quantity
            $table->decimal('rate', 15, 2)->default(0.00); // 💡 NEW: Unit Price/Rate
            $table->string('remarks')->nullable();
            // $table->decimal('total_cost', 15, 2)->storedAs('qty * rate'); // Optional: Add a virtual column for total cost
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boq_materials');
    }
};