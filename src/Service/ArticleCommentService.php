<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\CommentAbstracts;
use App\Interfaces\CommentableInterface;

/**
 * 对文章的评论
 *
 * Class ArticleCommentService
 * @package App\Service
 */
class ArticleCommentService extends CommentAbstracts implements CommentableInterface
{

}
