<?php

// database/migrations/YYYY_MM_DD_HHMMSS_update_users_table_add_name_fields_and_contact.php

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
        Schema::table('users', function (Blueprint $table) {
            // 1. Add the new columns
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact')->unique(); // 'contact' should be unique

            // 2. Drop the old 'name' column
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Add the old 'name' column back
            $table->string('name');

            // 2. Drop the new columns
            $table->dropColumn(['first_name', 'last_name', 'contact']);
        });
    }
};