<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('employee_number', 50)->nullable();
            $table->string('nip', 50)->nullable();
            $table->string('gender', 1)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'employee_number']);
            $table->unique(['school_id', 'nip']);
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nis', 50)->nullable();
            $table->string('nisn', 50)->nullable();
            $table->string('gender', 1)->nullable();
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'nis']);
            $table->unique(['school_id', 'nisn']);
        });

        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('parent_profile_id')->constrained('parent_profiles')->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->cascadeOnDelete();
            $table->string('relationship', 30)->default('Wali');
            $table->timestamps();

            $table->unique(['parent_profile_id', 'student_profile_id']);
            $table->index(['school_id', 'student_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parent_profiles');
        Schema::dropIfExists('student_profiles');
        Schema::dropIfExists('teacher_profiles');
    }
};
