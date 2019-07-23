<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Report as ReportObserver;
use Medoo\Medoo;

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

    /**
     * 获取被举报的内容列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest())
            ->field([
                'reporter_count' => Medoo::raw('COUNT(<report_id>)'),
                'reportable_id',
                'reportable_type',
            ])
            ->order('reporter_count', 'DESC')
            ->group([
                'reportable_id',
                'reportable_type',
            ])
            ->paginate();
    }
}
