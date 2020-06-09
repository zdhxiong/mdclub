<?php

/**
 * 评论列表添加字段
 *
 * @see /src/components/comments/actions.js 58 行
 * @param array $comments
 * @return array
 */
function comments_transformer(array $comments): array
{
  foreach ($comments as &$comment) {
    $comment['show_replies'] = false;
    $comment['show_new_reply'] = false;
    $comment['reply_submitting'] = false;
    $comment['replies_loading'] = false;
    $comment['replies_data'] = [];
    $comment['replies_pagination'] = null;
  }

  return $comments;
}
