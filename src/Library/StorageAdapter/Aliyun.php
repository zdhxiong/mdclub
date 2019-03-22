<?php

declare(strict_types=1);

namespace App\Library\StorageAdapter;

use App\Abstracts\ContainerAbstracts;
use App\Interfaces\ContainerInterface;
use App\Interfaces\StorageInterface;

/**
 * 阿里云 OSS 适配器
 *
 * Class Aliyun
 * @package App\Library\Storage\Adapter
 */
class Aliyun extends ContainerAbstracts implements StorageInterface
{
    /**
     * AliyunOSS constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        parent::__construct($container);

        [
            'storage_aliyun_access_id' => $AccessKeyId,
            'storage_aliyun_access_secret' => $AccessKeySecret,
            'storage_aliyun_bucket' => $bucket,
            'storage_aliyun_endpoint' => $endpoint,
        ] = $container->optionService->getMultiple();


    }

    /**
     * 发起请求
     *
     * @param string $method  请求方式
     * @param string $url     请求 URL
     * @param string $content 请求内容
     * @param array  $headers 请求 header
     */
    protected function request(string $method, string $url, string $content = null, array $headers = [])
    {
        $curl_handle = curl_init();

        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_FILETIME, true);
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
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);

        if (count($headers)) {
            $temp_headers = [];

            foreach ($headers as $k => $v) {
                $temp_headers[] = $k . ': ' . $v;
            }

            curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $temp_headers);
        }

        $output = curl_exec($curl_handle);
        curl_close($curl_handle);
    }

    /**
     * 写入文件
     *
     * @param  string $path
     * @param  string $content
     * @return bool
     */
    public function write(string $path, string $content): bool
    {
        $this->request('PUT', $path, $content);
    }

    /**
     * 删除文件
     *
     * @param  string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        $this->request('DELETE', $path);
    }

    /**
     * 删除多个文件
     *
     * @param  array $paths
     * @return bool
     */
    public function deleteMultiple(array $paths): bool
    {
        $this->request('POST', '/?delete');
    }
}
