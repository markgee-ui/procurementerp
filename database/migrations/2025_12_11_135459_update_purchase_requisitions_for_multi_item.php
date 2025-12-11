<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            // 1. Remove columns that moved to the PurchaseRequisitionItem model
            // NOTE: Check if these columns already exist before dropping!
            if (Schema::hasColumn('purchase_requisitions', 'boq_material_id')) {
                $table->dropForeign(['boq_material_id']);
                $table->dropColumn('boq_material_id');
            }
            if (Schema::hasColumn('purchase_requisitions', 'qty_requested')) {
                $table->dropColumn('qty_requested');
            }

            // 2. Add the total cost estimate column
            $table->decimal('cost_estimate', 12, 2)->nullable()->after('category'); 
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requisitions', function (Blueprint $table) {
            // Revert changes in reverse order

            // 1. Drop the new column
            if (Schema::hasColumn('purchase_requisitions', 'cost_estimate')) {
                $table->dropColumn('cost_estimate');
            }

            // 2. Add the old columns back (if necessary for rollback/testing)
            // This requires knowing the original types and constraints.
            // Assuming original types were foreignId and decimal:
            $table->foreignId('boq_material_id')->nullable()->after('boq_id')->constrained('boq_materials')->onDelete('set null');
            $table->decimal('qty_requested', 10, 2)->nullable()->after('boq_material_id');
        });
    }
};