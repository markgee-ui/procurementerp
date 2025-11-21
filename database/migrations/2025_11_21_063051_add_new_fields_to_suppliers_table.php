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
            // New fields for Supplier details (KRA Pin, Sales Contact, Photo URL)
            $table->string('kra_pin', 255)->nullable()->after('contact');
            $table->string('sales_person_contact', 255)->nullable()->after('kra_pin');
            // Using a longer string for URL storage
            $table->string('shop_photo_url', 500)->nullable()->after('sales_person_contact'); 

            // New fields for Payment details
            $table->string('account_number', 255)->nullable()->after('shop_photo_url');
            $table->string('bank_name', 255)->nullable()->after('account_number');
            $table->string('paybill_number', 255)->nullable()->after('bank_name');
            $table->string('till_number', 255)->nullable()->after('paybill_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'kra_pin', 
                'sales_person_contact', 
                'shop_photo_url', 
                'account_number', 
                'bank_name', 
                'paybill_number', 
                'till_number',
            ]);
        });
    }
};