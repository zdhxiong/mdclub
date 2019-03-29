<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Helper\RequestHelper;
use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;
use App\Traits\Url;
use Psr\Http\Message\StreamInterface;

/**
 * 又拍云适配器
 *
 * Class Upyun
 * @package App\Library\Storage\Adapter
 */
class Upyun extends AbstractAdapter implements StorageInterface
{
    use Url;

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

    /**
     * Upyun constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        [
            'storage_upyun_bucket' => $this->bucket,
            'storage_upyun_operator' => $this->operator,
            'storage_upyun_password' => $this->password,
        ] = $container->optionService->getMultiple();
    }

    /**
     * 获取请求头
     *
     * @param  string          $method
     * @param  string          $path
     * @param  array           $headers
     * @return array
     */
    protected function getRequestHeaders(string $method, string $path, array $headers = []): array
    {
        $operator = $this->operator;
        $password = md5($this->password);
        $bucket = $this->bucket;

        // 签名（https://help.upyun.com/knowledge-base/object_storage_authorization/#e4bba3e7a081e6bc94e7a4ba）
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $signature = base64_encode(hash_hmac('sha1', "{$method}&/{$bucket}/{$path}&{$date}", $password, true));
        $authorization = "UPYUN {$operator}:{$signature}";

        // headers
        $headers['Authorization'] = $authorization;
        $headers['Date'] = $date;

        array_walk($headers, function (&$item, $key) {
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
        $code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        if (intval($code) === 200) {
            return true;
        }

        throw new \Exception(json_decode($body, true)['msg']);
    }

    /**
     * 发起请求
     *
     * @param  string          $method
     * @param  string          $path
     * @param  StreamInterface $stream
     * @param  array           $headers
     * @return bool
     */
    protected function request(string $method, string $path, StreamInterface $stream = null, array $headers = []): bool
    {
        $url = "https://{$this->endpoint}/{$this->bucket}/{$path}";
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
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->container->request->getServerParam('HTTP_USER_AGENT'));
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);

        if ($stream) {
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $stream->getContents());
        }

        if (!$response = curl_exec($curl_handle)) {
            throw new \Exception('cURL resource: ' . (string)$curl_handle . ';cURL error: ' .curl_error($curl_handle). ' (' . curl_errno($curl_handle) . ')');
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
        $url = $this->getStorageUrl();
        $isSupportWebp = RequestHelper::isSupportWebp($this->container->request);
        $data['o'] = $url . $path;

        foreach ($thumbs as $size => [$width, $height]) {
            $params = "!/both/{$width}x{$height}";
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
        return $this->request('DELETE', $path, null, ['x-upyun-async' => true]);
    }
}
