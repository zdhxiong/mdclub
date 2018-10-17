<?php

declare(strict_types=1);

namespace App\Constant;

/**
 * 文件上传错误
 *
 * Class UploadErrorConstant
 * @package App\Constant
 */
class UploadErrorConstant
{
    /**
     * 获取错误描述
     *
     * @return array
     */
    public static function getMessage() {
        return [
            UPLOAD_ERR_INI_SIZE   => '上传的文件大小不能超过' . ini_get('upload_max_filesize'),
            UPLOAD_ERR_FORM_SIZE  => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
            UPLOAD_ERR_PARTIAL    => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE    => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION  => 'PHP 扩展程序停止了文件上载',
        ];
    }
}
