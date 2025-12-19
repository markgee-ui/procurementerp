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
    Schema::create('service_order_items', function (Blueprint $table) {
        $table->id();
        // Links back to the main Service Order
        $table->foreignId('service_order_id')->constrained()->onDelete('cascade');
        
        // Snapshots of data at time of order
        $table->decimal('unit_price', 15, 2);
        $table->decimal('discount', 5, 2)->default(0); // e.g., 10.50 for 10.5%
        $table->decimal('line_total', 15, 2);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_items');
    }
};
