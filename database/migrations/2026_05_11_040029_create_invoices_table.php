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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');  // 1-12
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('amount');  // dalam rupiah
            $table->date('due_date');
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'month', 'year']); // 1 tagihan per bulan per siswa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
