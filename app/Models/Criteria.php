<?php

namespace App\Models;

use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Criteria extends Model
{
    protected $fillable = [
        'name',
        'type',
        'p_threshold',
        'priority_value',
    ];

    protected $casts = [
        'type' => TypeEnum::class,
        'priority_value' => PriorityEnum::class,
    ];

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}
