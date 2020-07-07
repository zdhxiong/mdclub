<?php

declare(strict_types=1);

namespace MDClub\Library\StorageAdapter;

use Buzz\Client\Curl;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\SystemException;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Helper\Url;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as Psr7Request;

/**
 * 又拍云适配器
 */
class Upyun extends Abstracts implements Interfaces
{
    /**
     * 域名
     */
    protected $endpoint = 'v0.api.upyun.com';

    /**
     * 服务名称
     *
     * @var string
     */
    protected $bucket;

    /**
     * 操作员账号
     *
     * @var string
     */
    protected $operator;

    /**
     * 操作员密码
     *
     * @var string
     */
    protected $password;

    public function __construct()
    {
        $this->setPathPrefix(OptionConstant::STORAGE_UPYUN_DIR);

        $this->bucket = Option::get(OptionConstant::STORAGE_UPYUN_BUCKET);
        $this->operator = Option::get(OptionConstant::STORAGE_UPYUN_OPERATOR);
        $this->password = Option::get(OptionConstant::STORAGE_UPYUN_PASSWORD);
    }

    /**
     * 获取请求头
     *
     * @param  string $method
     * @param  string $path
     * @param  array  $headers
     * @return array
     */
    protected function getRequestHeaders(string $method, string $path, array $headers = []): array
    {
        $location = $this->applyPathPrefix($path);

        // 签名（https://help.upyun.com/knowledge-base/object_storage_authorization/#e4bba3e7a081e6bc94e7a4ba）
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $signature = base64_encode(hash_hmac(
            'sha1',
            "{$method}&/{$this->bucket}/{$location}&{$date}",
            md5($this->password),
            true
        ));
        $authorization = "UPYUN {$this->operator}:{$signature}";

        // headers
        $headers['Authorization'] = $authorization;
        $headers['Date'] = $date;

        return $headers;
    }

    /**
     * 发送请求
     *
     * @param string               $method
     * @param string               $path
     * @param StreamInterface|null $stream
     * @param array                $headers
     */
    protected function sendRequest(
        string $method,
        string $path,
        StreamInterface $stream = null,
        array $headers = []
    ): void {
        $location = $this->applyPathPrefix($path);

        if ($stream === null) {
            $stream = $this->getStreamFactory()->createStream();
        }

        $uri = $this->getUriFactory()->createUri("https://{$this->endpoint}/{$this->bucket}/{$location}");
        $headers = new Headers($this->getRequestHeaders($method, $location, $headers));
        $request = new Psr7Request($method, $uri, $headers, [], [], $stream);

        $client = new Curl(new ResponseFactory(), [
            'timeout' => 10,
            'verify' => false,
        ]);

        $response = $client->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
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
            $params = "!/both/{$width}x{$height}";
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

        $this->sendRequest('PUT', $location, $stream, ['Content-Length' => $stream->getSize()]);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path, array $thumbs): void
    {
        $location = $this->applyPathPrefix($path);

        $this->sendRequest('DELETE', $location, null, ['x-upyun-async' => 'true']);
    }
}
