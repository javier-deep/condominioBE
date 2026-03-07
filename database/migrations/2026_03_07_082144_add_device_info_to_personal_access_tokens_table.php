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
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('device_name')->nullable()->after('name');
            $table->string('ip_address')->nullable()->after('device_name');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->timestamp('logged_in_at')->nullable()->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn(['device_name', 'ip_address', 'user_agent', 'logged_in_at']);
        });
    }
};
