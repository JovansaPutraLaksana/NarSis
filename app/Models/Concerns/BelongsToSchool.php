<?php

namespace App\Models\Concerns;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    protected static function bootBelongsToSchool(): void
    {
        static::addGlobalScope(
            'school',
            function (Builder $builder) {

                if (! Auth::check()) {
                    return;
                }

                $user = Auth::user();

                if (! $user->school_id) {
                    return;
                }

                $builder->where(
                    $builder->getModel()->getTable().'.school_id',
                    $user->school_id
                );
            }
        );


        static::creating(function ($model) {

            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();

            if (
                $user->school_id &&
                empty($model->school_id)
            ) {
                $model->school_id = $user->school_id;
            }

        });
    }


    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}