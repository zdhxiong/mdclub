<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Report as ReportObserver;

/**
 * 举报模型
 */
class Report extends Abstracts
{
    public $table = 'report';
    public $primaryKey = 'report_id';
    protected $timestamps = true;
    protected $observe = ReportObserver::class;

    protected const UPDATE_TIME = false;

    public $columns = [
        'report_id',
        'reportable_id',
        'reportable_type',
        'user_id',
        'reason',
        'create_time',
    ];

    public $allowFilterFields = [
        'reportable_type'
    ];
}
