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
        Schema::table('purchases', function (Blueprint $table) {
            $table->softDeletes();

            $table->unique(['date', 'deleted_at']);
        });

        Schema::table('purchase_records', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('accountings', function (Blueprint $table) {
            $table->softDeletes();

            $table->unique(['date', 'deleted_at']);
        });

        Schema::table('accounting_records', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropUnique(['date', 'deleted_at']);
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_records', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('accountings', function (Blueprint $table) {
            $table->dropUnique(['date', 'deleted_at']);
            $table->dropSoftDeletes();
        });

        Schema::table('accounting_records', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
