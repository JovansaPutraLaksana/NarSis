<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'school_id', 'teaching_assignment_id', 'teacher_profile_id', 'title', 'description', 'file_path', 'file_name', 'due_at', 'max_score', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'published_at' => 'datetime',
            'max_score' => 'decimal:2',
        ];
    }

    public function teachingAssignment(): BelongsTo
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class, 'teacher_profile_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
