<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use BelongsToSchool;

    public const STATUSES = [
        'present' => 'Hadir',
        'sick' => 'Sakit',
        'permission' => 'Izin',
        'absent' => 'Alpa',
    ];

    protected $fillable = ['school_id', 'attendance_session_id', 'student_profile_id', 'status', 'note'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}
