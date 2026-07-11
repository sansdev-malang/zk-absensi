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
        Schema::create('daily_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('shift_detail_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('jam_masuk')->nullable();
            $table->dateTime('jam_pulang')->nullable();
            $table->integer('menit_terlambat')->default(0);
            $table->integer('menit_pulang_cepat')->default(0);
            $table->string('status_kehadiran')->default('Hadir'); // Hadir, Terlambat, Alfa, Libur
            $table->decimal('bonus_didapat', 12, 2)->default(0);
            $table->timestamps();
            
            // Satu orang hanya punya satu rekap kehadiran per hari
            $table->unique(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attendances');
    }
};
