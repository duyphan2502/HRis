<?php

namespace HRis\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Timelog extends Model
{
    protected $fillable = [
        'type_id',
        'holiday_id',
        'employee_id',
        'schedule_id',
        'in',
        'out',
        'rendered_hours',
    ];

    protected $dates = ['in', 'out'];
}
