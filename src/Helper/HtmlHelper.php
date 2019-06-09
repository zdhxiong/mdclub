<?php

declare(strict_types=1);

namespace App\Helper;

use Markdownify\Converter;

/**
 * HTML 相关方法
 */
class HtmlHelper
{
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
            $converter = new Converter;
        }

        return $converter->parseString($html);
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

        $config = \HTMLPurifier_Config::createDefault();
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

        $purifier = new \HTMLPurifier($config);

        return $purifier->purify($html);
    }
}
