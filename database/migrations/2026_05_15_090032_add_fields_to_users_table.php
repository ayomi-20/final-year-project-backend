<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Add only if not already present — safe to run
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('id');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('users', 'contact')) {
                $table->string('contact', 9)->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['tourist', 'provider', 'admin'])->default('tourist')->after('contact');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'login_otp')) {
                $table->string('login_otp', 6)->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'login_otp_created_at')) {
                $table->timestamp('login_otp_created_at')->nullable()->after('login_otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'contact',
                'role', 'avatar', 'login_otp', 'login_otp_created_at',
            ]);
        });
    }
};