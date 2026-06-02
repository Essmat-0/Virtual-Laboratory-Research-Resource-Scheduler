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
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255);
        });
        Schema::table('equipment', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('equipment_categories')->nullOnDelete();
            $table->string('location_code', 100)->nullable();
            $table->float('total_usage_hours')->default(0);
            $table->float('calibration_threshold')->nullable(); // if usageHours >= calibration => maintenance
            $table->integer('cooldown_buffer')->default(0);
        });
        Schema::table('certifications', function (Blueprint $table) {
            $table->foreignId('equipment_category_id')->after('expiry_date')->nullable()->constrained('equipment_categories')->cascadeOnDelete();
        });
        Schema::create('safety_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->nullOnDelete();
            $table->boolean('acknowledgment_status')->default(false);
            $table->ipAddress('user_ip')->nullable();
            $table->timestamps();
        });
        //safety_logs table is like a proof that the user has approved the Policies and Checklists 
        //just before booking

        Schema::create('Interlock_proxies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->string('access_level'); //Admin - Maintenance - researcher - pi === it checks status then act on it.
        });

        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('stock_level')->default(0);
        });
        Schema::create('equipment_consumables', function (Blueprint $table) {
            $table->foreignId('equipment_id')->constrained('equipment')->restrictOnDelete();
            $table->foreignId('consumable_id')->constrained('consumables')->cascadeOnDelete();
            $table->primary(['equipment_id', 'consumable_id']);
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->ipAddress('user_ip')->nullable();
        });
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->nullOnDelete();
            $table->foreignId('safety_log_id')->nullable()->constrained('safety_logs')->nullOnDelete();
            $table->foreignId('grant_id')->nullable()->constrained('grants')->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('equipment_consumables');
        Schema::dropIfExists('Interlock_proxies');
        Schema::dropIfExists('safety_logs');
        Schema::dropIfExists('consumables');

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropColumn('user_ip');
        });

        Schema::table('certifications', function (Blueprint $table) {
            $table->dropForeign(['equipment_category_id']);
            $table->dropColumn('equipment_category_id');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'location_code',
                'total_usage_hours',
                'calibration_threshold',
                'cooldown_buffer'
            ]);
        });

        Schema::dropIfExists('equipment_categories');
    }
};