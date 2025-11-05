<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_validated')->default(false)->after('password');
            $table->string('role')->default('enseignant')->after('is_validated');
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_validated', 'role', 'bank_name', 'bank_account']);
        });
    }
};
