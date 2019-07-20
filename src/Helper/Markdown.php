<?php

declare(strict_types=1);

namespace MDClub\Helper;

class Markdown
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
        if ($parsedown === null) {
            $parsedown = new \Parsedown();
        }

        return $parsedown->text($markdown);
    }
}
