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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('remember_token');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('city', 120)->nullable()->after('birth_date');
            $table->string('country', 120)->nullable()->after('city');
            $table->string('occupation', 120)->nullable()->after('country');
            $table->text('bio')->nullable()->after('occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'birth_date', 'city', 'country', 'occupation', 'bio']);
        });
    }
};
