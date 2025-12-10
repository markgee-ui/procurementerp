<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('user_id')->constrained('users'); // PM who initiated the request
            
            // 💡 CHANGE 1: Renamed 'project_id' to 'boq_id' to link to the BoQ header
            $table->foreignId('boq_id')->constrained('boqs'); 
            
            $table->foreignId('boq_material_id')->constrained('boq_materials'); // Specific material item

            // Requisition Details (These fields are stored directly from the BoqMaterial lookup)
            $table->string('item_name', 255); // 💡 ADDED: Item name from BoqMaterial
            $table->string('unit', 50);       // 💡 ADDED: Unit from BoqMaterial
            $table->decimal('qty_requested', 15, 2);
            $table->decimal('cost_estimate', 15, 2)->nullable(); // 💡 ADDED: Estimated cost (qty * rate)
            
            // Additional details
            $table->date('required_by_date')->nullable(); // 💡 MADE NULLABLE for flexibility
            $table->text('justification')->nullable();
            
            // 💡 REMOVED 'category' default as it can be derived or is less critical than the item data

            // Workflow & Status
            $table->string('status', 50)->default('Pending'); // 💡 ADDED string length
            $table->unsignedSmallInteger('current_stage')->default(1); 
            $table->text('approval_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};