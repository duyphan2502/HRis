<?php

namespace HRis\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SalaryComponents
 * @package HRis\Eloquent
 */
class SalaryComponent extends Model
{

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'salary_components';

    /**
     * @return array
     */
    function getSalaryAndSSS()
    {
        $salaryComponents = self::where('name', 'LIKE', '%Basic%')
            ->orWhere('name', 'LIKE', '%SSS%')
            ->orderBy('id', 'asc')
            ->get(['id'])
            ->take(2);

        return ['monthlyBasic' => $salaryComponents->first()->id, 'SSS' => $salaryComponents->last()->id];
        
    }

}
