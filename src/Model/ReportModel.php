<?php

declare(strict_types=1);

namespace App\Model;

use App\Abstracts\ModelAbstracts;

/**
 * Class ReportModel
 * @package App\Model
 */
class ReportModel extends ModelAbstracts
{
    protected $table = 'report';
    protected $primaryKey = 'report_id';
    protected $timestamps = true;

    const UPDATE_TIME = false;

    public $columns = [
        'report_id',
        'reportable_id',
        'reportable_type',
        'user_id',
        'reason',
        'create_time',
    ];
}
