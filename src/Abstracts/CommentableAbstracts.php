<?php

declare(strict_types=1);

namespace App\Abstracts;

use Psr\Container\ContainerInterface;
use App\Service\Service;

/**
 * 评论抽象类
 *
 * Class CommentAbstracts
 * @package App\Abstracts
 */
abstract class CommentableAbstracts extends Service
{
    /**
     * 评论类型（question、answer、article）
     *
     * @var string
     */
    protected $commentableType;

    /**
     * 评论目标的 Model 实例
     */
    protected $commentableTargetModel;

    /**
     * 评论目标的 Service 实例
     */
    protected $commentableTargetService;

    /**
     * CommentableAbstracts constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->commentableTargetModel = $this->{$this->commentableType . 'Model'};
        $this->commentableTargetService = $this->{$this->commentableType . 'Service'};
    }

    /**
     * 获取评论列表
     *
     * @param  int   $commentableId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getComments(int $commentableId, bool $withRelationship = false): array
    {

    }

    /**
     * 添加评论
     *
     * @param  int    $commentableId
     * @param  string $content
     * @return int
     */
    public function addComment(int $commentableId, string $content = null): int
    {

    }
}
