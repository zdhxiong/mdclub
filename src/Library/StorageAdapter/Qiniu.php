<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use Buzz\Message\FormRequestBuilder;
use InvalidArgumentException;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\SystemException;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Helper\Url;
use Psr\Http\Message\StreamInterface;

/**
 * 七牛云适配器
 */
class Qiniu extends Abstracts implements Interfaces
{
    /**
     * 存储区域和域名的映射
     */
    protected static $zones = [
        'z0' => 'up.qiniup.com',
        'z1' => 'up-z1.qiniup.com',
        'z2' => 'up-z2.qiniup.com',
        'na0' => 'up-na0.qiniup.com',
        'as0' => 'up-as0.qiniup.com',
    ];

    /**
     * 当前存储区域
     *
     * @var string
     */
    protected $zone;

    /**
     * accessKey
     *
     * @var string
     */
    protected $accessKey;

    /**
     * secretKey
     *
     * @var string
     */
    protected $secretKey;

    /**
     * 存储空间
     *
     * @var string
     */
    protected $bucket;

    public function __construct()
    {
        $this->setPathPrefix(OptionConstant::STORAGE_QINIU_DIR);

        $this->accessKey = Option::get(OptionConstant::STORAGE_QINIU_ACCESS_ID);
        $this->secretKey = Option::get(OptionConstant::STORAGE_QINIU_ACCESS_SECRET);
        $this->bucket = Option::get(OptionConstant::STORAGE_QINIU_BUCKET);
        $this->zone = Option::get(OptionConstant::STORAGE_QINIU_ZONE);
    }

    /**
     * URL 安全的 Base64 编码
     *
     * @param  string $data
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1231/appendix#urlsafe-base64
     */
    protected function base64Encode(string $data): string
    {
        return str_replace(['+', '/'], ['-', '_'], base64_encode($data));
    }

    /**
     * 获取上传策略
     *
     * @param  string $location
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1206/put-policy
     */
    protected function getPolicy(string $location): string
    {
        $policy = [
            'scope' => "{$this->bucket}:{$location}",
            'deadline' => time() + 3600,
        ];

        return $this->base64Encode(json_encode($policy));
    }

    /**
     * 获取上传凭证
     *
     * @param  string $location
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1208/upload-token
     */
    protected function getUploadToken(string $location): string
    {
        $policy = $this->getPolicy($location);
        $sign = $this->base64Encode(hash_hmac('sha1', $policy, $this->secretKey, true));

        return "{$this->accessKey}:{$sign}:{$policy}";
    }

    /**
     * 获取管理凭证
     *
     * @param  string $location
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1201/access-token
     */
    protected function getAccessToken(string $location): string
    {
        $sign = $this->base64Encode(hash_hmac('sha1', "{$location}\n", $this->secretKey, true));

        return "{$this->accessKey}:{$sign}";
    }

    /**
     * @inheritDoc
     */
    public function get(string $path, array $thumbs): array
    {
        $storagePath = Url::storagePath();
        $isSupportWebp = Request::isSupportWebp();
        $data['original'] = $storagePath . $path;

        foreach ($thumbs as $size => [$width, $height]) {
            $params = "?imageView2/1/w/{$width}/h/{$height}";
            $params .= $isSupportWebp ? '/format/webp' : '';

            $data[$size] = "{$storagePath}{$path}{$params}";
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);
        $zones = self::$zones;
        $headers = [ 'Host' => $zones[$this->zone] ];

        $builder = new FormRequestBuilder();
        $builder->addField('key', $location);
        $builder->addField('token', $this->getUploadToken($location));
        $builder->addFile('file', (string) $stream->getMetadata('uri'));

        $response = $this->getBrowser()->submitForm(
            "https://{$zones[$this->zone]}/",
            $builder->build(),
            'POST',
            $headers
        );

        if ($response->getStatusCode() !== 200) {
            throw new SystemException($response->getReasonPhrase());
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);
        $encodedEntryURI = $this->base64Encode("{$this->bucket}:{$location}");

        $headers = [
            'Host' => 'rs.qiniu.com',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "QBox {$this->getAccessToken("/delete/{$encodedEntryURI}")}"
        ];

        try {
            $response = $this->getBrowser()->post("https://rs.qiniu.com/delete/{$encodedEntryURI}", $headers);

            if ($response->getStatusCode() !== 200) {
                throw new SystemException($response->getReasonPhrase());
            }
        } catch (InvalidArgumentException $e) {
        } // 612: 待删除资源不存在
    }
}
