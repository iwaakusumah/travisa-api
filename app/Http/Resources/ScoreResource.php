<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student_name' => $this->student ? $this->student->name : null,
            'period_name' => $this->period ? $this->period->name : null,
            'criteria' => [
                'criteria_name' => $this->criteria ? $this->criteria->name : null,
                'value' => $this->value
            ]
        ];
    }
}
