<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Exception\SystemException;
use App\Interfaces\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * 阿里云 OSS 适配器
 */
class Aliyun extends AbstractAdapter implements StorageInterface
{
    /**
     * AccessKey ID
     *
     * @var string
     */
    protected $accessKeyId;

    /**
     * Access Key Secret
     *
     * @var string
     */
    protected $accessKeySecret;

    /**
     * Bucket 名称
     *
     * @var string
     */
    protected $bucket;

    /**
     * EndPoint（地域节点）
     *
     * @var string
     */
    protected $endpoint;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->accessKeyId = $this->optionService->storage_aliyun_access_id;
        $this->accessKeySecret = $this->optionService->storage_aliyun_access_secret;
        $this->bucket = $this->optionService->storage_aliyun_bucket;
        $this->endpoint = $this->optionService->storage_aliyun_endpoint;
    }

    /**
     * 获取请求头
     *
     * @param  string  $method
     * @param  string  $path
     * @param  array   $headers
     * @return array
     */
    protected function getRequestHeaders(string $method, string $path, array $headers = []): array
    {
        $accessKeyId = $this->accessKeyId;
        $accessKeySecret = $this->accessKeySecret;

        // 签名（https://help.aliyun.com/document_detail/31951.html?spm=a2c4g.11186623.6.1097.b2c43bdbx35G1S）
        $contentType = $method === 'PUT' ? 'application/x-www-form-urlencoded' : 'application/octet-stream';
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $canonicalizedResource = "/{$this->bucket}/$path";
        $signature  = base64_encode(hash_hmac('sha1', "{$method}\n\n{$contentType}\n{$date}\n{$canonicalizedResource}", $accessKeySecret, true));
        $authorization = "OSS {$accessKeyId}:{$signature}";

        // headers
        $headers['Authorization'] = $authorization;
        $headers['Date'] = $date;
        $headers['Host'] = "{$this->bucket}.{$this->endpoint}";
        $headers['Content-Type'] = $contentType;

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

        if (in_array($code, [200, 201, 204, 206], true)) {
            return true;
        }

        preg_match('/<Message>(.*?)<\/Message>/', $body, $matches);
        throw new SystemException($matches[1]);
    }

    /**
     * 发起请求
     *
     * @param  string          $method   请求方式
     * @param  string          $path     请求 URL
     * @param  StreamInterface $stream   请求内容
     * @param  array           $headers
     * @return string|false
     */
    protected function request(string $method, string $path, StreamInterface $stream = null, array $headers = []): bool
    {
        $url = "https://{$this->bucket}.{$this->endpoint}/$path";
        $headers = $this->getRequestHeaders($method, $path, $headers);

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

        if ($stream) {
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $stream->getContents());
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
            $params = "?x-oss-process=image/resize,m_fill,w_{$width},h_{$height},limit_0";
            $params .= $isSupportWebp ? '/format,webp' : '';

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
        return $this->request('PUT', $path, $stream, ['Content-Length' => $stream->getSize()]);
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
        return $this->request('DELETE', $path);
    }
}
