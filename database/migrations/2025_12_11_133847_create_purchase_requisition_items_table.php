<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            $table->id();

            // Linkage to Parent PR
            $table->foreignId('purchase_requisition_id')
                  ->constrained('purchase_requisitions')
                  ->onDelete('cascade'); 
            
            // Linkage to BoQ Line Item
            $table->foreignId('boq_material_id')
                  ->nullable() 
                  ->constrained('boq_materials')
                  ->onDelete('restrict');

            // Linkage to BoQ Activity (FIXED: references 'boq_activities' table)
            $table->foreignId('boq_activity_id') 
                  ->nullable()
                  ->constrained('boq_activities')
                  ->onDelete('restrict');

            // Snapshot data from BoQ
            $table->string('item_name');
            $table->string('unit')->nullable();

            // Financials
            $table->decimal('qty_requested', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable(); 
            $table->decimal('cost_estimate', 10, 2); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};