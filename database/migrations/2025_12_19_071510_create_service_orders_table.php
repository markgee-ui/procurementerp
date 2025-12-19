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
    Schema::create('service_orders', function (Blueprint $table) {
        $table->id();
        // Link to the existing suppliers table
        $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
        
        $table->string('order_number')->unique(); // e.g., TSO-0001
        $table->string('project_name');
        $table->text('service_description');
        $table->decimal('total_amount', 15, 2);
        
        $table->string('status')->default('Draft'); // Draft, Approved, Paid
        $table->date('order_date');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
