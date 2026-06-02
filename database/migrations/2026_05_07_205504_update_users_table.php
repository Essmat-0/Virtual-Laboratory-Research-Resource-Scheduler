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
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('is_active');
            $table->boolean('is_active')->after('clearance_level')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};