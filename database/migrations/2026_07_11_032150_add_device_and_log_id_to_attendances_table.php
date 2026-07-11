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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('user_id')->constrained('devices')->nullOnDelete();
            $table->string('uid_log')->nullable()->after('device_id');
            // Membuat kombinasi unik agar log tidak masuk berkali-kali
            $table->unique(['user_id', 'waktu', 'uid_log']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->dropUnique(['user_id', 'waktu', 'uid_log']);
            $table->dropColumn(['device_id', 'uid_log']);
        });
    }
};
