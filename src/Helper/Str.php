<?php

declare(strict_types=1);

namespace MDClub\Helper;

use HTMLPurifier;
use HTMLPurifier_Config;
use Markdownify\Converter;
use Parsedown;

/**
 * 把存储容量转换为字节，如 200MB 转换为整型字节
 *
 * @param  string $memory
 * @return int
 */
function memoryToByte(string $memory): int
{
    $memory = strtoupper(str_replace(' ', '', $memory));
    [$number, $unit] = preg_split("/([0-9\.]+)/", $memory, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $number = (int) $number;

    $units = [
        ['B'],
        ['KB', 'K'],
        ['MB', 'M'],
        ['GB', 'G'],
        ['TB', 'T'],
    ];
    $result = 0;

    foreach ($units as $index => $unitArr) {
        if (in_array(strtoupper($unit), $unitArr)) {
            $result = $number * pow(1024, $index);
        }
    }

    return $result;
}

/**
 * 字符串相关方法
 */
class Str
{
    /**
     * 检查字符串是否是日期格式
     *
     * @param  string $dateString
     * @return bool
     */
    public static function isDate(string $dateString): bool
    {
        return strtotime(date('Y-m-d', strtotime($dateString))) === strtotime($dateString);
    }

    /**
     * 把存储容量数值格式化为可读的格式
     *
     * @param  int    $memory
     * @return string
     */
    public static function memoryFormat(int $memory): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pos = 0;

        while ($memory >= 1024) {
            $memory /= 1024;
            $pos++;
        }

        return round($memory, 2) . ' ' . $units[$pos];
    }

    /**
     * 比较两个存储容量的大小，第一个大于第二个，则返回1，小于第二个则返回-1，相等则返回0
     *
     * @param  string $memory1
     * @param  string $memory2
     * @return int
     */
    public static function memoryCompare(string $memory1, string $memory2): int
    {
        return memoryToByte($memory1) <=> memoryToByte($memory2);
    }

    /**
     * 下划线转驼峰
     *
     * @param  string $str
     * @param  string $separator
     * @return string
     */
    public static function toCamelize($str, $separator = '_')
    {
        $str = $separator . str_replace($separator, ' ', strtolower($str));

        return ltrim(str_replace(' ', '', ucwords($str)), $separator);
    }

    /**
     * 生成 guid
     *
     * @return string
     */
    public static function guid(): string
    {
        if (function_exists('com_create_guid')) {
            $guid = trim(com_create_guid(), '{}');

            return strtolower(str_replace('-', '', $guid));
        }

        mt_srand((int)microtime() * 10000);

        return md5(uniqid((string)mt_rand(), true));
    }

    /**
     * 获取随机字符串
     *
     * @param  int     $length  字符串长度
     * @param  string  $chars   字符的集合
     * @return string
     */
    public static function random(int $length, string $chars = ''): string
    {
        if (!$chars) {
            $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        }

        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $result;
    }

    /**
     * HTML 转 Markdown
     *
     * @param  string $html  HTML 字符串
     * @return string        Markdown 字符串
     */
    public static function toMarkdown(string $html): string
    {
        if (!$html) {
            return $html;
        }

        static $converter;
        if ($converter === null) {
            $converter = new Converter();
        }

        return $converter->parseString($html);
    }

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
            $parsedown = new Parsedown();
        }

        return $parsedown->text($markdown);
    }

    /**
     * 过滤 HTML
     *
     * @param  string  $html  需要过滤的字符串
     * @return string         过滤后的字符串
     */
    public static function removeXss(string $html): string
    {
        if (!$html) {
            return $html;
        }

        $allow_tags = [
            'a[href][target][title][rel]',
            'abbr[title]',
            'address',
            'b',
            'big',
            'blockquote[cite]',
            'br',
            'caption',
            'center',
            'cite',
            'code',
            'col[align][valign][span][width]',
            'colgroup[align][valign][span][width]',
            'dd',
            'del',
            'div',
            'dl',
            'dt',
            'em',
            'font[color][size][face]',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hr',
            'i',
            'img[src][alt][title][width][height]',
            'ins',
            'li',
            'ol',
            'p',
            'pre',
            's',
            'small',
            'span',
            'sub',
            'sup',
            'strong',
            'table[width][border][align][valign]',
            'tbody[align][valign]',
            'td[width][rowspan][colspan][align][valign]',
            'tfoot[align][valign]',
            'th[width][rowspan][colspan][align][valign]',
            'thead[align][valign]',
            'tr[align][valign]',
            'tt',
            'u',
            'ul',
            'figure',
            'figcaption',
        ];

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', implode(',', $allow_tags));
        $def = $config->getHTMLDefinition(true);
        $def->addElement('audio', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
            'autoplay' => 'Bool',
            'controls' => 'Bool',
            'loop'     => 'Bool',
            'preload'  => 'Bool',
            'src'      => 'URI',
        ]);
        $def->addElement('figure', 'Block', 'Flow', 'Common');
        $def->addElement('figcaption', 'Block', 'Flow', 'Common');
        $def->addElement('mark', 'Inline', 'Inline', 'Common');
        $def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
            'autoplay' => 'Bool',
            'controls' => 'Bool',
            'loop'     => 'Bool',
            'preload'  => 'Bool',
            'src'      => 'URI',
            'height'   => 'Length',
            'width'    => 'Length',
        ]);

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($html);
    }
}
