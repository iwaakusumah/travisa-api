<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\LevelEnum;
use App\Enums\MajorEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'class_id', 'name', 'gender', 'major', 'level'
    ];

    protected $casts = [
        'gender' => GenderEnum::class,
        'major' => MajorEnum::class,
        'level' => LevelEnum::class,
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
