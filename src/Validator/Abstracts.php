<?php

declare(strict_types=1);

namespace MDClub\Validator;

use BadMethodCallException;
use MDClub\Exception\SystemException;
use MDClub\Exception\ValidationException;
use MDClub\Facade\Service\TopicService;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 验证器抽象类
 *
 * @method self eachID()
 * @method self eachString()
 * @method self eachEmail()
 */
abstract class Abstracts
{
    /**
     * 字段名和对应的中文名的数组
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 字段名和对应的错误信息的数组
     *
     * @var array
     */
    protected $errors = [];

    /**
     * 需要验证的数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 当前验证的字段名
     *
     * @var string
     */
    protected $field;

    /**
     * 设置当前字段的错误消息
     *
     * @param string $message
     */
    protected function setError(string $message): void
    {
        $this->errors[$this->field] = $message;
    }

    /**
     * 当前字段是否已存在错误
     *
     * @return bool
     */
    protected function isHasError(): bool
    {
        return isset($this->errors[$this->field]);
    }

    /**
     * 数据中是否不存在当前字段
     *
     * @return bool
     */
    protected function isExist(): bool
    {
        return isset($this->data[$this->field]);
    }

    /**
     * 为数值ID
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isID($data): bool
    {
        return is_int($data) && $data > 0;
    }

    /**
     * 为空字符串、空数组、或 null
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isEmpty($data): bool
    {
        return $data === '' || $data === null || $data = [];
    }

    /**
     * 为字符串
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isString($data): bool
    {
        return is_string($data);
    }

    /**
     * 是否是邮箱格式
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isEmail($data): bool
    {
        return !!filter_var($data, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 当前字段是否无需再验证
     *
     * @return bool
     */
    protected function skip(): bool
    {
        return $this->isHasError() || !$this->isExist();
    }

    /**
     * 错误消息中的占位符替换
     *
     * @param string $message
     *
     * @return string
     */
    private function interpolate(string $message): string
    {
        $replace = [];

        foreach ($this->attributes as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }

    /**
     * 设置需要验证的数据
     *
     * @param array $data
     *
     * @return $this
     */
    protected function data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * 设置需要验证的字段
     *
     * @param string $name
     *
     * @return $this
     */
    protected function field(string $name): self
    {
        $this->field = $name;

        return $this;
    }

    /**
     * 获取当前验证的值
     *
     * @return mixed
     */
    protected function value()
    {
        return $this->data[$this->field];
    }

    /**
     * 设置当前字段的值
     *
     * @param mixed $value
     */
    protected function setValue($value): void
    {
        $this->data[$this->field] = $value;
    }

    /**
     * 参数存在，即通过 isset() 判断为 true
     *
     * @return $this
     */
    protected function exist(): self
    {
        if ($this->isHasError()) {
            return $this;
        }

        if (!$this->isExist()) {
            $this->setError("参数 {$this->field} 必须存在");
        }

        return $this;
    }

    /**
     * 参数不为空，即不为空字符串、空数组、或 null
     *
     * @return $this
     */
    protected function notEmpty(): self
    {
        if ($this->skip()) {
            return $this;
        }

        if ($this->isEmpty($this->value())) {
            $this->setError("{{$this->field}}不能为空");
        }

        return $this;
    }

    /**
     * 验证为数值ID
     *
     * @return $this
     */
    protected function ID(): self
    {
        if ($this->skip()) {
            return $this;
        }

        if (!$this->isID($this->value())) {
            $this->setError("{$this->field} 必须是正整数");
        }

        return $this;
    }

    /**
     * 验证为字符串类型
     *
     * @return $this
     */
    protected function string(): self
    {
        if ($this->skip()) {
            return $this;
        }

        if (!$this->isString($this->value())) {
            $this->setError("{$this->field} 必须是字符串");
        }

        return $this;
    }

    /**
     * 验证为数组类型
     *
     * @return $this
     */
    protected function array(): self
    {
        if ($this->skip()) {
            return $this;
        }

        if (!is_array($this->value())) {
            $this->setError("{$this->field} 必须是数组");
        }

        return $this;
    }

    /**
     * 验证字符串长度
     *
     * @param int $min
     * @param int $max
     *
     * @return $this
     */
    protected function length(int $min = null, int $max = null): self
    {
        if ($this->string()->skip()) {
            return $this;
        }

        if (is_null($min) && is_null($max)) {
            throw new SystemException('length 方法必须指定 $min 或 $max');
        }

        $length = mb_strlen(
            $this->value(),
            mb_detect_encoding($this->value())
        );

        if (is_null($min)) {
            if ($length > $max) {
                $this->setError("{{$this->field}}不能超过 ${max} 个字符");
            }
        } elseif (is_null($max)) {
            if ($length < $min) {
                $this->setError("{{$this->field}}不能少于 ${min} 个字符");
            }
        } elseif ($length < $min || $length > $max) {
            $this->setError("{{$this->field}}的字符长度应在 ${min} 至 ${max} 之间");
        }

        return $this;
    }

    /**
     * 验证数组长度
     *
     * @param int $min
     * @param int $max
     *
     * @return $this
     */
    protected function arrayLength(int $min = null, int $max = null): self
    {
        if ($this->array()->skip()) {
            return $this;
        }

        if (is_null($min) && is_null($max)) {
            throw new SystemException('arrayLength 方法必须指定 $min 或 $max');
        }

        $length = count($this->value());

        if (is_null($min)) {
            if ($length > $max) {
                $this->setError("{$this->field} 数组不能超过 ${max} 个元素");
            }
        } elseif (is_null($max)) {
            if ($length < $min) {
                $this->setError("{$this->field} 数组不能少于 ${min} 个元素");
            }
        } elseif ($length < $min || $length > $max) {
            $this->setError("{$this->field} 数组的元素数量应在 ${min} 至 ${max} 之间");
        }

        return $this;
    }

    /**
     * 必须在数组中
     *
     * @param array $range
     *
     * @return $this
     */
    protected function in(array $range): self
    {
        if ($this->array()->skip()) {
            return $this;
        }

        if (!in_array($this->value(), $range)) {
            $this->setError("{{$this->field}}必须是 " . implode(', ', $range) . '之一');
        }

        return $this;
    }

    /**
     * 数组中的话题ID，在数据库中都存在
     *
     * @return $this
     */
    protected function topicIdsExist(): self
    {
        if ($this->eachID()->skip()) {
            return $this;
        }

        $arr = TopicService::hasMultiple($this->value());
        $notExistIds = [];

        foreach ($arr as $topicId => $exist) {
            if (!$exist) {
                $notExistIds[] = $topicId;
            }
        }

        if ($notExistIds) {
            $this->setError("话题ID " . implode(', ', $notExistIds) . ' 不存在');
        }

        return $this;
    }

    /**
     * 邮箱格式
     *
     * @return $this
     */
    protected function email(): self
    {
        if ($this->string()->notEmpty()->skip()) {
            return $this;
        }

        if (!$this->isEmail($this->value())) {
            $this->setError('邮箱格式错误');
        }

        return $this;
    }

    /**
     * 验证是否为上传图片
     *
     * @param bool $static 是否限制只允许上传 png, jpg 图片
     *
     * @return $this
     */
    protected function uploadedImage(bool $static = false): self
    {
        if ($this->skip()) {
            return $this;
        }

        $file = $this->value();

        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => '上传的文件大小不能超过' . ini_get('upload_max_filesize'),
            UPLOAD_ERR_FORM_SIZE  => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
            UPLOAD_ERR_PARTIAL    => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE    => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION  => 'PHP 扩展程序停止了文件上载',
        ];

        if (!($file instanceof UploadedFileInterface)) {
            $this->setError("请选择要上传的图片");
        } elseif ($file->getError() !== UPLOAD_ERR_OK) {
            $this->setError($uploadErrors[$file->getError()]);
        } elseif ($static && !in_array($file->getClientMediaType(), ['image/jpeg', 'image/png'])) {
            $this->setError('仅允许上传 jpg 和 png 格式的图片');
        } elseif (!$static && !in_array($file->getClientMediaType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            $this->setError('仅允许上传 jpg, png 和 gif 格式的图片');
        }

        return $this;
    }

    /**
     * 移除字符串两端的空格和换行符
     *
     * @return $this
     */
    protected function trim(): self
    {
        if ($this->string()->skip()) {
            return $this;
        }

        $value = trim($this->value());
        $this->setValue($value);

        return $this;
    }

    /**
     * 移除字符串中的 HTML 标签
     *
     * @return $this
     */
    protected function stripTags(): self
    {
        if ($this->string()->skip()) {
            return $this;
        }

        $value = strip_tags($this->value());
        $this->setValue($value);

        return $this;
    }

    /**
     * 转换为 HTML 实体
     *
     * @return $this
     */
    protected function htmlentities(): self
    {
        if ($this->string()->skip()) {
            return $this;
        }

        $value = htmlentities($this->value());
        $this->setValue($value);

        return $this;
    }

    /**
     * 过滤数组中的重复元素
     *
     * @return $this
     */
    protected function arrayUnique(): self
    {
        if ($this->array()->skip()) {
            return $this;
        }

        $value = array_values(array_unique($this->value()));
        $this->setValue($value);

        return $this;
    }

    /**
     * 若 isset(field) 为 false，则设置默认值
     *
     * @param mixed $value
     *
     * @return $this
     */
    protected function defaultValue($value): self
    {
        if ($this->skip()) {
            return $this;
        }

        if (!$this->isExist()) {
            $value = is_callable($value) ? $value() : $value;
            $this->setValue($value);
        }

        return $this;
    }

    /**
     * 检查是否通过验证
     *
     * @param bool $needCaptcha 下次验证是否需要输入验证码
     *
     * @return array
     * @throws ValidationException
     */
    protected function validate(bool $needCaptcha = false)
    {
        if ($this->errors) {
            foreach ($this->errors as &$error) {
                $error = $this->interpolate($error);
            }

            throw new ValidationException($this->errors, $needCaptcha);
        }

        $handledData = $this->data;

        $this->errors = [];
        $this->data = [];
        $this->field = '';

        return $handledData;
    }

    /**
     * 魔术方法
     *
     * 包含：对数组中的每个元素进行验证的方法
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call(string $name, array $arguments): self
    {
        $arrayValidators = [
            'eachID' => "{$this->field} 数组的每个元素都必须是正整数",
            'eachString' => "{$this->field} 数组的每个元素都必须是字符串",
            'eachEmail' => "{$this->field} 数组的每个元素都必须是邮箱"
        ];

        if (!in_array($name, array_keys($arrayValidators), true)) {
            throw new BadMethodCallException('Call to undefined method ' . self::class . '::' . $name);
        }

        if ($this->array()->skip()) {
            return $this;
        }

        $determiner = 'is' . substr($name, 4);

        foreach ($this->value() as $value) {
            if (!$this->$determiner($value)) {
                $this->setError($arrayValidators[$name]);

                return $this;
            }
        }

        return $this;
    }
}
