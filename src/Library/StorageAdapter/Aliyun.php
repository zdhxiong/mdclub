<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use Buzz\Client\Curl;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\SystemException;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Helper\Url;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as Psr7Request;
use Psr\Http\Message\StreamInterface;

/**
 * 阿里云 OSS 适配器
 */
class Aliyun extends Abstracts implements Interfaces
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

    public function __construct()
    {
        $this->accessKeyId = Option::get(OptionConstant::STORAGE_ALIYUN_ACCESS_ID);
        $this->accessKeySecret = Option::get(OptionConstant::STORAGE_ALIYUN_ACCESS_SECRET);
        $this->bucket = Option::get(OptionConstant::STORAGE_ALIYUN_BUCKET);
        $this->endpoint = Option::get(OptionConstant::STORAGE_ALIYUN_ENDPOINT);
    }

    /**
     * 获取请求头
     *
     * @param  string          $method
     * @param  string          $path
     * @param  StreamInterface $stream
     * @return array
     */
    protected function getRequestHeaders(string $method, string $path, StreamInterface $stream): array
    {
        // 签名（https://help.aliyun.com/document_detail/31951.html?spm=a2c4g.11186623.6.1097.b2c43bdbx35G1S）
        $contentType = $method === 'PUT' ? 'application/x-www-form-urlencoded' : 'application/octet-stream';
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $canonicalizedResource = "/{$this->bucket}$path";
        $signature  = base64_encode(hash_hmac(
            'sha1',
            "{$method}\n\n{$contentType}\n{$date}\n{$canonicalizedResource}",
            $this->accessKeySecret,
            true
        ));
        $authorization = "OSS {$this->accessKeyId}:{$signature}";

        return [
            'Authorization' => $authorization,
            'Date' => $date,
            'Host' => "{$this->bucket}.{$this->endpoint}",
            'Content-Type' => $contentType,
            'Content-Length' => $stream->getSize(),
        ];
    }

    /**
     * 发送请求
     *
     * @param  string               $method
     * @param  string               $path
     * @param  StreamInterface|null $stream
     */
    protected function sendRequest(string $method, string $path, StreamInterface $stream = null): void
    {
        if ($stream === null) {
            $stream = $this->getStreamFactory()->createStream();
        }

        $uri = $this->getUriFactory()->createUri("https://{$this->bucket}.{$this->endpoint}/$path");
        $headers = new Headers($this->getRequestHeaders($method, $path, $stream));
        $request = new Psr7Request($method, $uri, $headers, [], [], $stream);

        $client = new Curl(new ResponseFactory(), [
            'timeout' => 10,
            'verify' => false,
        ]);

        $response = $client->sendRequest($request);

        if (!in_array($response->getStatusCode(), [200, 201, 204, 206], true)) {
            throw new SystemException($response->getReasonPhrase());
        }
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
            $params = "?x-oss-process=image/resize,m_fill,w_{$width},h_{$height},limit_0";
            $params .= $isSupportWebp ? '/format,webp' : '';

            $data[$size] = "{$storagePath}{$path}{$params}";
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, StreamInterface $stream, array $thumbs): void
    {
        $this->sendRequest('PUT', $path, $stream);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $this->sendRequest('DELETE', $path);
    }
}
