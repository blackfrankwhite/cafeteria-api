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
        Schema::create('accounting_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->nullable()->constrained('entities')->onDelete('cascade');
            $table->float('amount');
            $table->float('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_records');
    }
};
