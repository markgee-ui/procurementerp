<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_purchase_orders_table.php

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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            
            // Link to the Supplier
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            
            // Core PO Details
            $table->string('order_number')->unique()->nullable(); // You can auto-generate this later
            $table->date('order_date')->default(now());
            $table->date('required_by_date')->nullable();
            
            // Financials
            $table->decimal('total_amount', 10, 2)->default(0.00);
            
            // Status and Notes
            $table->string('status')->default('Draft'); // e.g., Draft, Issued, Completed
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
