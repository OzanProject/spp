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
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->after('invoice_id');
            $table->string('method')->default('transfer')->after('amount');
            $table->string('status')->default('pending')->after('proof');
            $table->renameColumn('notes', 'note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('note', 'notes');
            $table->dropColumn(['amount', 'method', 'status']);
        });
    }
};
