<?php

namespace App\Enums;

enum UserRole: string
{
    case WebsiteAdmin = 'website_admin';
    case SchoolAdmin = 'school_admin';
    case Teacher = 'teacher';
    case Student = 'student';
    case Parent = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::WebsiteAdmin => 'Admin Website',
            self::SchoolAdmin => 'Admin Sekolah',
            self::Teacher => 'Guru',
            self::Student => 'Siswa',
            self::Parent => 'Orang Tua',
        };
    }
}