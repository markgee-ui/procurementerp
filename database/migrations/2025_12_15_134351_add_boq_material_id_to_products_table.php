<?php

// database/migrations/YYYY_MM_DD_HHMMSS_add_boq_material_id_to_products_table.php

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
        Schema::table('products', function (Blueprint $table) {
            // Define the foreign key column to link to the BoqMaterial table
            $table->foreignId('boq_material_id')
                  ->nullable() // Allow nulls if old records exist without a link
                  ->constrained('boq_materials') // Ensure it references the boq_materials table
                  ->after('supplier_id'); // Place it after supplier_id for clean structure
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropConstrainedForeignId('boq_material_id');
            
            // Drop the column itself
            $table->dropColumn('boq_material_id');
        });
    }
};
