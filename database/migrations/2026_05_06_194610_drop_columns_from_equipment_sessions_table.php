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
        Schema::table('equipment_sessions', function (Blueprint $table) {
            $columns = ['status', 'approval_status'];
            $table->dropColumn($columns);
            $table->boolean('stock_reduced')->default(false);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment_sessions', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->string('approval_status')->nullable();
        });
    }
};