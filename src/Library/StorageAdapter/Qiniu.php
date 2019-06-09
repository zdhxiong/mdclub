<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Exception\SystemException;
use App\Interfaces\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 七牛云适配器
 */
class Qiniu extends AbstractAdapter implements StorageInterface
{
    /**
     * 存储区域和域名的映射
     */
    static protected $zones = [
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

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->accessKey = $this->optionService->storage_qiniu_access_id;
        $this->secretKey = $this->optionService->storage_qiniu_access_secret;
        $this->bucket = $this->optionService->storage_qiniu_bucket;
        $this->zone = $this->optionService->storage_qiniu_zone;
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
     * @param  string $path
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1206/put-policy
     */
    protected function getPolicy(string $path): string
    {
        $policy = [
            'scope' => "{$this->bucket}:{$path}",
            'deadline' => time() + 3600,
        ];

        return $this->base64Encode(json_encode($policy));
    }

    /**
     * 获取上传凭证
     *
     * @param  string $path
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1208/upload-token
     */
    protected function getUploadToken(string $path): string
    {
        $policy = $this->getPolicy($path);
        $sign = $this->base64Encode(hash_hmac('sha1', $policy, $this->secretKey, true));

        return "{$this->accessKey}:{$sign}:{$policy}";
    }

    /**
     * 获取管理凭证
     *
     * @param  string $path
     * @return string
     * @link https://developer.qiniu.com/kodo/manual/1201/access-token
     */
    protected function getAccessToken(string $path): string
    {
        $sign = $this->base64Encode(hash_hmac('sha1', "{$path}\n", $this->secretKey, true));

        return "{$this->accessKey}:{$sign}";
    }

    /**
     * 获取请求头
     *
     * @param  array   $headers
     * @return array
     */
    protected function getRequestHeaders(array $headers = []): array
    {
        array_walk($headers, static function (&$item, $key) {
            $item = "{$key}: {$item}";
        });

        return array_values($headers);
    }

    /**
     * 处理 curl 返回值
     *
     * @param  $curl_handle
     * @param  $response
     * @return bool
     */
    protected function handleResponse($curl_handle, $response): bool
    {
        $header_size = curl_getinfo($curl_handle, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        $code = (int) curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        if (in_array($code, [200, 612], true)) { // 612: 待删除资源不存在
            return true;
        }

        throw new SystemException(json_decode($body, true)['error']);
    }

    /**
     * 发起请求
     *
     * @param  string          $method    请求方式
     * @param  string          $url       请求 URL
     * @param  array           $postData  请求内容
     * @param  array           $headers
     * @return string|false
     */
    protected function request(string $method, string $url, array $postData = [], array $headers = []): bool
    {
        $headers = $this->getRequestHeaders($headers);

        $curl_handle = curl_init();

        curl_setopt($curl_handle, CURLOPT_FILETIME, true);
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, false);
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl_handle, CURLOPT_HEADER, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5184000);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_NOSIGNAL, true);
        curl_setopt($curl_handle, CURLOPT_REFERER, $url);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->request->getServerParam('HTTP_USER_AGENT'));
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);

        if ($postData) {
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postData);
        }

        if (!$response = curl_exec($curl_handle)) {
            throw new SystemException('cURL resource: ' . $curl_handle . ';cURL error: ' .curl_error($curl_handle). ' (' . curl_errno($curl_handle) . ')');
        }

        $response = $this->handleResponse($curl_handle, $response);

        curl_close($curl_handle);

        return $response;
    }

    /**
     * 获取图片 URL
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return array
     */
    public function get(string $path, array $thumbs): array
    {
        $url = $this->urlService->storage();
        $isSupportWebp = $this->requestService->isSupportWebp();
        $data['o'] = $url . $path;

        foreach ($thumbs as $size => [$width, $height]) {
            $params = "?imageView2/1/w/{$width}/h/{$height}";
            $params .= $isSupportWebp ? '/format/webp' : '';

            $data[$size] = "{$url}{$path}{$params}";
        }

        return $data;
    }

    /**
     * 写入文件
     *
     * @param  string          $path
     * @param  StreamInterface $stream
     * @param  array           $thumbs
     * @return bool
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): bool
    {
        $zones = self::$zones;
        $postData = [
            'key' => $path,
            'token' => $this->getUploadToken($path),
            'file' => new \CURLFile($stream->getMetadata('uri')),
        ];

        $headers = [
            'Host' => $zones[$this->zone],
        ];

        return $this->request('POST', "https://{$zones[$this->zone]}/", $postData, $headers);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @param  array  $thumbs
     * @return bool
     */
    public function delete(string $path, array $thumbs): bool
    {
        $encodedEntryURI = $this->base64Encode("{$this->bucket}:{$path}");
        $headers = [
            'Host' => 'rs.qiniu.com',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => "QBox {$this->getAccessToken("/delete/{$encodedEntryURI}")}"
        ];

        return $this->request('POST', "https://rs.qiniu.com/delete/{$encodedEntryURI}", [], $headers);
    }
}
