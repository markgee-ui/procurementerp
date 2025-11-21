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
        Schema::table('suppliers', function (Blueprint $table) {
            // Rename the existing column 'shop_photo_url' to 'shop_photo_path'
            $table->renameColumn('shop_photo_url', 'shop_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Rename the column back to 'shop_photo_url' if rolling back
            $table->renameColumn('shop_photo_path', 'shop_photo_url');
        });
    }
};