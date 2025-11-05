<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users','phone')) $table->string('phone')->nullable();
            if (!Schema::hasColumn('users','establishment')) $table->string('establishment')->nullable();
            if (!Schema::hasColumn('users','subject')) $table->string('subject')->nullable();
            if (!Schema::hasColumn('users','experience_years')) $table->unsignedInteger('experience_years')->nullable();
            if (!Schema::hasColumn('users','settings')) $table->json('settings')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['phone','establishment','subject','experience_years','settings'] as $col) {
                if (Schema::hasColumn('users',$col)) $table->dropColumn($col);
            }
        });
    }
};
