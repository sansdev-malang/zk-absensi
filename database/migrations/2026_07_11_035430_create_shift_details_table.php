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
        Schema::create('shift_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->tinyInteger('hari'); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->boolean('is_cross_day')->default(false);
            $table->timestamps();
            
            $table->unique(['shift_id', 'hari']); // 1 shift hanya punya 1 jam kerja spesifik untuk hari tertentu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_details');
    }
};
