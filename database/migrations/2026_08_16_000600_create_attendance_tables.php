<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules')->restrictOnDelete();
            $table->foreignId('opened_by')->constrained('users')->restrictOnDelete();
            $table->date('attendance_date');
            $table->timestamp('opened_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['schedule_id', 'attendance_date']);
            $table->index(['school_id', 'attendance_date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('attendance_session_id')->constrained('attendance_sessions')->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->restrictOnDelete();
            $table->string('status', 20); // present, sick, permission, absent
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique(['attendance_session_id', 'student_profile_id']);
            $table->index(['student_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sessions');
    }
};
