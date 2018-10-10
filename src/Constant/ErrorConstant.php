<?php

declare(strict_types=1);

namespace App\Constant;

/**
 * 错误代码
 *
 * Class ErrorConstant
 * @package App\Constant
 */
class ErrorConstant
{
    // ====================================
    // ==================================== 系统级错误
    // ====================================

    /**
     * 系统错误
     */
    const SYSTEM_ERROR                = [100000, '服务器错误'];

    /**
     * 系统维护中
     */
    const SYSTEM_MAINTAIN             = [100001, '系统维护中'];

    /**
     * IP 受限
     */
    const SYSTEM_IP_LIMIT             = [100002, 'IP 受限'];

    /**
     * 接口不存在
     */
    const SYSTEM_API_NOT_FOUNT        = [100001, '接口不存在'];

    /**
     * 请求的 HTTP METHOD 不支持
     */
    const SYSTEM_API_NOT_ALLOWED      = [100000, '该接口不支持此 HTTP METHOD'];

    /**
     * 字段验证失败
     */
    const SYSTEM_FIELD_VERIFY_FAILED  = [100002, '字段验证失败'];

    /**
     * 邮件发送失败
     */
    const SYSTEM_SEND_EMAIL_FAILED    = [100003, '邮件发送失败'];

    /**
     * 邮件验证码已过期
     */
    const SYSTEM_EMAIL_VERIFY_EXPIRED = [100004, '邮件验证码已失效'];

    /**
     * 图片上传失败
     */
    const SYSTEM_IMAGE_UPLOAD_FAILED  = [100005, '图片上传失败'];

    // ====================================
    // ==================================== 服务级错误
    // ====================================

    /**
     * Token 参数不能为空
     */
    const USER_TOKEN_EMPTY            = [200002, 'token 不能为空'];

    /**
     * Token 已失效（包括 Token 已过期和 Token 记录不存在）
     */
    const USER_TOKEN_FAILED           = [200003, 'token 已失效'];

    /**
     * Token 对应的记录不存在
     */
    const USER_TOKEN_NOT_FOUND        = [200004, 'token 对于的记录不存在'];

    /**
     * 需要管理员权限
     */
    const USER_NEED_MANAGE_PERMISSION = [200005, '需要管理员权限'];

    /**
     * 指定用户不存在
     */
    const USER_NOT_FOUND              = [200006, '指定用户不存在'];

    /**
     * 目标用户不存在
     */
    const USER_TARGET_NOT_FOUNT       = [200007, '目标用户不存在'];

    /**
     * 指定用户已被禁用
     */
    const USER_HAS_BEEN_DISABLED      = [200013, '该用户已被禁用'];

    /**
     * 账号或密码错误
     */
    const USER_PASSWORD_ERROR         = [200014, '账号或密码错误'];

    /**
     * 头像上传失败
     */
    const USER_AVATAR_UPLOAD_FAILED   = [200008, '头像上传失败'];

    /**
     * 用户封面上传失败
     */
    const USER_COVER_UPLOAD_FAILED    = [200009, '封面上传失败'];

    /**
     * 已经关注该用户
     */
    const USER_ALREADY_FOLLOWING      = [200010, '已经关注该用户'];

    /**
     * 尚未关注该用户
     */
    const USER_NOT_FOLLOWING          = [200011, '尚未关注该用户'];

    /**
     * 不能关注你自己
     */
    const USER_CANT_FOLLOW_YOURSELF   = [200012, '不能关注你自己'];

    // ====================================
    // ==================================== 问题相关错误
    // ====================================
    /**
     * 指定的问题不存在
     */
    const QUESTION_NOT_FOUND          = [300001, '指定问题不存在'];

    /**
     * 已经关注了该问题
     */
    const QUESTION_ALREADY_FOLLOWING  = [300002, '已经关注该问题'];

    /**
     * 尚未关注该问题
     */
    const QUESTION_NOT_FOLLOWING      = [300003, '尚未关注该问题'];

    /**
     * 指定的回答不存在
     */
    const ANSWER_NOT_FOUNT            = [310001, '指定回答不存在'];

    /**
     * 回答的指定评论不存在
     */
    const ANSWER_COMMENT_NOT_FOUNT    = [320001, '指定的评论不存在'];

    // ====================================
    // ==================================== 话题相关错误
    // ====================================
    /**
     * 指定的话题不存在
     */
    const TOPIC_NOT_FOUND             = [400001, '指定话题不存在'];

    /**
     * 话题封面上传失败
     */
    const TOPIC_COVER_UPLOAD_FAILED   = [400002, '话题封面上传失败'];

    /**
     * 已经关注了该话题
     */
    const TOPIC_ALREADY_FOLLOWING     = [400003, '已关注该话题'];

    /**
     * 尚未关注该话题
     */
    const TOPIC_NOT_FOLLOWING         = [400004, '尚未关注该话题'];

    // ====================================
    // ===================================== 文章相关错误
    // ====================================
    /**
     * 指定的文章不存在
     */
    const ARTICLE_NOT_FOUND           = [500001, '指定文章不存在'];

    /**
     * 已关注该文章
     */
    const ARTICLE_ALREADY_FOLLOWING   = [500002, '已关注该文章'];

    /**
     * 未关注该文章
     */
    const ARTICLE_NOT_FOLLOWING       = [500003, '尚未关注该文章'];
}
