<?php

declare(strict_types=1);

namespace App\Helper;

class MarkdownHelper
{

    /**
     * Markdown 转 HTML
     *
     * @param  string $markdown  Markdown 字符串
     * @return string            HTML 字符串
     */
    public static function toHtml(string $markdown): string
    {
        if (!$markdown) {
            return $markdown;
        }

        static $parsedown;
        if (is_null($parsedown)) {
            $parsedown = new \Parsedown();
        }

        return $parsedown->text($markdown);
    }
}
