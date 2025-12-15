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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to link back to the Purchase Requisition
            $table->foreignId('purchase_requisition_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Foreign key for the user who performed the action (QS, OPM, etc.)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict'); 

            // The stage number (1=QS, 2=OPM, 3=Procurement)
            $table->unsignedSmallInteger('stage');
            
            // The action taken: 'approved' or 'rejected'
            $table->string('status', 20); 

            // Notes, especially important for rejections
            $table->text('notes')->nullable(); 

            $table->timestamps();
            
            // Ensures a user only approves/rejects a PR once per stage (optional constraint)
            $table->unique(['purchase_requisition_id', 'stage']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};