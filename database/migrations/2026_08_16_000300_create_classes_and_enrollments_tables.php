<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teacher_profiles')->nullOnDelete();
            $table->string('name', 100);
            $table->string('grade_level', 20)->nullable();
            $table->string('room', 50)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id', 'name']);
        });

        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->restrictOnDelete();
            $table->date('enrolled_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['school_id', 'academic_year_id', 'student_profile_id']);
            $table->index(['school_class_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
        Schema::dropIfExists('school_classes');
    }
};
