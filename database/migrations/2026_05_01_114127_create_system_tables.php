<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('status', 50);
            $table->decimal('hourly_rate', 10, 2)->default(0.00);
            $table->integer('required_clearance')->default(0);
            $table->timestamps();
        });

        Schema::create('equipment_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['Pending', 'Active', 'Completed', 'Cancelled'])->default('Pending');
            $table->string('approval_status', 50)->nullable();
            $table->timestamps();
        });


        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('equipment_sessions')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('normalized_amount', 10, 2)->nullable();
            $table->boolean('is_split')->default(false);
            $table->timestamps();
        });
        Schema::create('pi_profiles', function (Blueprint $table) {

            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->float('budget_limit')->nullable();
            $table->string('affiliation')->nullable();
        });

        Schema::create('labm_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('managed_Lab_Locations')->nullable();
        });

        Schema::create('auditor_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('audit_scope')->nullable();
        });

        Schema::create('researcher_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('academicLevel')->nullable();
            $table->foreignId('pis_id')->nullable()->constrained('pi_profiles', 'user_id')->nullOnDelete();
        });
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(table: 'researcher_profiles', column: 'user_id')->cascadeOnDelete();
            $table->date('expiry_date');
            $table->timestamps();
        });

        Schema::create('grants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('pi_id')->nullable()->constrained('pi_profiles', 'user_id')->nullOnDelete();
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->dateTime('expiry_date');
            $table->timestamps();
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->decimal('cost', 10, 2);
            $table->text('description'); // Changed to text for longer logs
            $table->timestamps();
        });

        Schema::create('publication_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('pi_id')->constrained('pi_profiles', 'user_id')->cascadeOnDelete();
            $table->string('doi', 100);
            $table->timestamps();
        });

        Schema::create('roi_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->decimal('roi_score', 10, 2);
            $table->string('recommendation', 50);
            $table->timestamps();
        });

        Schema::create('utilization_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->decimal('usage_percentage', 5, 2);
            $table->timestamps();
        });

        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('transaction_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('grant_id')->constrained()->cascadeOnDelete();
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS session_summary");
        Schema::dropIfExists('audit_trails');
        Schema::dropIfExists('utilization_cache');
        Schema::dropIfExists('roi_reports');
        Schema::dropIfExists('publication_links');
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('grants');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('roles');
    }
};