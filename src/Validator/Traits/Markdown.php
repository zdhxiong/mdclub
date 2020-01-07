<?php

declare(strict_types=1);

namespace MDClub\Validator\Traits;

use MDClub\Helper\Str;

/**
 * 支持 markdown 格式的验证和处理
 */
trait Markdown
{
    /**
     * 获取 markdown 字段名
     *
     * @return string
     */
    protected function getMarkdownField(): string
    {
        return $this->field . '_markdown';
    }

    /**
     * 获取渲染后的字段名
     *
     * @return string
     */
    protected function getRenderedField(): string
    {
        return $this->field . '_rendered';
    }

    /**
     * 是否跳过对 markdown 字段的验证
     *
     * @return bool
     */
    protected function markdownSkip(): bool
    {
        $markdownField = $this->getMarkdownField();
        $renderedField = $this->getRenderedField();

        $hasError =
            isset($this->errors[$markdownField]) ||
            isset($this->errors[$renderedField]);

        $notExist =
            !isset($this->data[$markdownField]) &&
            !isset($this->data[$renderedField]);

        return $hasError || $notExist;
    }

    /**
     * 存在 _markdown 或 _rendered 之一
     *
     * @return $this
     */
    protected function markdownExist(): self
    {
        if ($this->markdownSkip()) {
            return $this;
        }

        $markdownField = $this->getMarkdownField();
        $renderedField = $this->getRenderedField();

        $notExist =
            !isset($this->data[$markdownField]) &&
            !isset($this->data[$renderedField]);

        if ($notExist) {
            $this->errors[$markdownField]
                = $this->errors[$renderedField]
                = "\{${markdownField}\}不能为空";
        }

        return $this;
    }

    /**
     * 支持 markdown 的字段的处理
     *
     * 若指定字段名为 content，则会对 content_markdown 和 content_rendered 两个字段进行处理
     * content_markdown 和 content_rendered 至少需传入一个；都传入时，以 content_markdown 为准
     *
     * @param int $max markdown文本的最长字符数
     *
     * @return $this
     */
    protected function markdownSupport(int $max = null): self
    {
        if ($this->markdownSkip()) {
            return $this;
        }

        $markdownField = $this->getMarkdownField();
        $renderedField = $this->getRenderedField();

        $markdownData = $this->data[$markdownField] ?? '';
        $renderedData = $this->data[$renderedField] ?? '';

        // 必须是字符串
        if (!is_string($markdownData)) {
            $this->errors[$markdownField] = '该字段必须是字符串';
        }

        if (!is_string($renderedData)) {
            $this->errors[$renderedField] = '该字段必须是字符串';
        }

        if ($this->markdownSkip()) {
            return $this;
        }

        // _markdown 和 _rendered 不能同时为空
        $markdownData = Str::removeXss($markdownData);
        $renderedData = Str::removeXss($renderedData);

        if (!$markdownData && !$renderedData) {
            $this->errors[$markdownField] = "\{${markdownField}\}不能为空";
            $this->errors[$renderedField] = "\{${renderedField}\}不能为空";
        } elseif (!$markdownData) {
            $markdownData = Str::toMarkdown($renderedData);
        } else {
            $renderedData = Str::toHtml($markdownData);
        }

        if ($this->markdownSkip()) {
            return $this;
        }

        // 验证长度，以 rendered 格式去掉 HTML 标签后的长度为准
        if ($max && mb_strlen(strip_tags($renderedData), mb_detect_encoding($renderedData)) > $max) {
            $this->errors[$markdownField] = "\{${markdownField}\}不能超过 ${max} 个字符";
            $this->errors[$renderedField] = "\{${renderedField}\}不能超过 ${max} 个字符";
        }

        if ($this->markdownSkip()) {
            return $this;
        }

        // 存储处理后的数据
        $this->data[$markdownField] = $markdownData;
        $this->data[$renderedField] = $renderedData;

        return $this;
    }
}
