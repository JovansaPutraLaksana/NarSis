<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->string('code', 30);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('teacher_profile_id')->constrained('teacher_profiles')->restrictOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['school_id', 'semester_id', 'school_class_id', 'subject_id'], 'teaching_assignment_unique');
            $table->index(['teacher_profile_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_assignments');
        Schema::dropIfExists('subjects');
    }
};
