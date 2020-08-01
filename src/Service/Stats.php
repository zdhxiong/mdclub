<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Db;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\AnswerModel;
use MDClub\Facade\Model\ArticleModel;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Model\QuestionModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Helper\Str;
use MDClub\Initializer\App;

/**
 * 数据统计
 */
class Stats
{
    /**
     * 获取系统信息
     *
     * @return array
     */
    protected function systemInfo(): array
    {
        $dbInfo = Db::info();

        $dbSizeSQL = "
         SELECT sum(DATA_LENGTH)+sum(INDEX_LENGTH) as size
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA='mdclub_test'
        ";
        $dbSize = (int) Db::query($dbSizeSQL)->fetchColumn(0);

        return [
            'mdclub_version' => App::$version,
            'os_version' => php_uname('s') . ' ' . php_uname('r'),
            'php_version' => PHP_VERSION,
            'webserver_version' => explode(' ', $_SERVER['SERVER_SOFTWARE'])[0],
            'database_version' => "${dbInfo['driver']} ${dbInfo['version']}",
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . ' 秒',
            'disk_free_space' => Str::memoryFormat((int) disk_free_space(".")),
            'database_size' => Str::memoryFormat($dbSize),
        ];
    }

    /**
     * 获取用户总数
     *
     * @return int
     */
    protected function totalUser(): int
    {
        return UserModel::count();
    }

    /**
     * 获取提问总数
     *
     * @return int
     */
    protected function totalQuestion(): int
    {
        return QuestionModel::count();
    }

    /**
     * 获取文章总数
     *
     * @return int
     */
    protected function totalArticle(): int
    {
        return ArticleModel::count();
    }

    /**
     * 获取回答总数
     *
     * @return int
     */
    protected function totalAnswer(): int
    {
        return AnswerModel::count();
    }

    /**
     * 获取评论总数
     *
     * @return int
     */
    protected function totalComment(): int
    {
        return CommentModel::count();
    }

    /**
     * 获取各种数据的新增记录
     *
     * @param  string $table
     * @return array
     */
    private function getNewData(string $table): array
    {
        $queryParams = Request::getQueryParams();

        // 起止日期
        $startDate = isset($queryParams['start_date']) && Str::isDate($queryParams['start_date'])
            ? $queryParams['start_date']
            : date("Y-m-d", strtotime("-6 day"));
        $endDate = isset($queryParams['end_date']) && Str::isDate($queryParams['end_date'])
            ? $queryParams['end_date']
            : date("Y-m-d");

        $startTimestamp = strtotime("${startDate} 00:00:00");
        $endTimestamp = strtotime("${endDate} 23:59:59");
        $tablePrefix = App::$config['DB_PREFIX'];

        $queryResult = Db::query("
            SELECT from_unixtime(create_time,'%Y-%m-%d') date,COUNT(*) count
            FROM ${tablePrefix}${table}
            WHERE create_time >= ${startTimestamp} AND create_time <= ${endTimestamp}
            GROUP BY from_unixtime(create_time,'%y-%m-%d')
        ")->fetchAll();

        $queryResult = array_column($queryResult, 'count', 'date');

        // 没有数据的日期填充 0
        $days = round(($endTimestamp - $startTimestamp) / 86400);
        $dates = [];
        $result = [];

        for ($i = 0; $i < $days; $i++) {
            $dates[] = date('Y-m-d', $startTimestamp + (86400 * $i));
        }

        foreach ($dates as $date) {
            $result[] = [
                'date' => $date,
                'count' => $queryResult[$date] ?? 0,
            ];
        }

        return $result;
    }

    /**
     * 获取用户新增记录
     *
     * @return array
     */
    protected function newUser(): array
    {
        return $this->getNewData('user');
    }

    /**
     * 获取提问新增记录
     *
     * @return array
     */
    protected function newQuestion(): array
    {
        return $this->getNewData('question');
    }

    /**
     * 获取文章新增记录
     *
     * @return array
     */
    protected function newArticle(): array
    {
        return $this->getNewData('article');
    }

    /**
     * 获取回答新增记录
     *
     * @return array
     */
    protected function newAnswer(): array
    {
        return $this->getNewData('answer');
    }

    /**
     * 获取评论新增记录
     *
     * @return array
     */
    protected function newComment(): array
    {
        return $this->getNewData('comment');
    }

    /**
     * 获取统计数据
     *
     * @return array
     */
    public function get(): array
    {
        $availableIncludes = [
            'system_info',
            'total_user',
            'total_question',
            'total_article',
            'total_answer',
            'total_comment',
            'new_user',
            'new_question',
            'new_article',
            'new_answer',
            'new_comment',
        ];

        $includes = Request::getQueryParams()['include'] ?? '';
        $includes = array_intersect($availableIncludes, explode(',', $includes));

        $result = [];

        foreach ($includes as $include) {
            $method = Str::toCamelize($include);
            $result[$include] = $this->$method();
        }

        return $result;
    }
}
