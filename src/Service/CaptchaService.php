<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use Gregwar\Captcha\CaptchaBuilder;
use App\Exception\ValidationException;
use App\Helper\StringHelper;

/**
 * 图形验证码
 *
 * 1. 调用 build() 方法生成一个新的图形验证码，返回 token 和 image
 * 2. 前端提交用户输入的验证码 code 和 token，服务端调用 check() 检查 code 和 token 是否匹配
 * 3. 无论是否匹配，每个验证码都只能验证一次，验证后即删除
 *
 * Class CaptchaService
 *
 * @package App\Service
 */
class CaptchaService extends ServiceAbstracts
{
    /**
     * 验证码有效期
     *
     * @var int
     */
    protected $lifeTime = 3600;

    /**
     * 生成图形验证码。每次调用都会生成一个新的验证码。
     *
     * @param  int   $width  验证码图片宽度
     * @param  int   $height 验证码图片高度
     * @return array
     */
    public function build(int $width = 100, int $height = 36): array
    {
        $builder = new CaptchaBuilder();
        $builder->build($width, $height);

        $code = $builder->getPhrase();
        $token = StringHelper::guid();
        $cacheKey = $this->getCacheKey($token);

        $this->cache->set($cacheKey, $code, $this->lifeTime);

        return [
            'image' => $builder->inline(),
            'token' => $token,
        ];
    }

    /**
     * 检查验证码是否正确，无论正确与否，检查过后直接删除记录
     *
     * @param  string $token 验证码请求token
     * @param  string $code  验证码字符
     * @return bool
     */
    public function check(string $token, string $code): bool
    {
        $cacheKey = $this->getCacheKey($token);
        $correctCode = $this->cache->get($cacheKey);

        if (!$correctCode) {
            return false;
        }

        $this->cache->delete($cacheKey);

        return $correctCode == $code;
    }

    /**
     * 下一次请求是否需要验证码
     *
     * @param  string    $id        区别用户身份的字符串
     * @param  string    $action    操作名称
     * @param  int       $max_count 最多操作次数
     * @param  int       $period    在该时间范围内
     * @return bool
     * @throws ValidationException
     */
    public function isNextTimeNeed(string $id, string $action, int $max_count, int $period): bool
    {
        $remaining = $this->throttleService->getRemaining($id, $action, $max_count, $period);
        $needCaptcha = $remaining <= 1;

        $captchaToken = $this->request->getParsedBodyParam('captcha_token');
        $captchaCode = $this->request->getParsedBodyParam('captcha_code');

        if ($remaining <= 0 && (!$captchaToken || !$captchaCode || !$this->check($captchaToken, $captchaCode))) {
            throw new ValidationException([
                'captcha_code' => '图形验证码错误'
            ], $needCaptcha);
        }

        return $needCaptcha;
    }

    /**
     * 获取缓存键名
     *
     * @param  string $key
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return "captcha_{$key}";
    }
}
