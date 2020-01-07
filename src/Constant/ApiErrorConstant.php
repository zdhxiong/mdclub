<?php

declare(strict_types=1);

namespace MDClub\Constant;

/**
 * 错误代码
 *
 * 错误码格式：A-BB-CCC
 * A：错误级别，1：系统级错误；2：服务级错误
 * B：模块编号
 * C：具体错误编号
 */
class ApiErrorConstant
{
    /**
     * 系统级错误
     */
    public const SYSTEM_ERROR                     = [100000, '服务器错误']; // 未知错误
    public const SYSTEM_MAINTAIN                  = [100001, '系统维护中']; // 管理员手动设置成系统维护
    public const SYSTEM_IP_LIMIT                  = [100002, 'IP 请求超过上限'];
    public const SYSTEM_USER_LIMIT                = [100003, '用户请求超过上限'];
    public const SYSTEM_API_NOT_FOUND             = [100004, '接口不存在'];
    public const SYSTEM_API_NOT_ALLOWED           = [100005, '该接口不支持此 HTTP METHOD'];
    public const SYSTEM_REQUEST_JSON_INVALID      = [100006, '请求参数的 json 格式错误'];

    /**
     * 通用服务错误，模块编号：0
     */
    public const COMMON_FIELD_VERIFY_FAILED       = [200001, '字段验证失败']; // 具体错误字段和错误信息在 errors 字段中
    public const COMMON_SEND_EMAIL_FAILED         = [200002, '邮件发送失败'];
    public const COMMON_EMAIL_VERIFY_EXPIRED      = [200003, '邮件验证码已失效'];
    public const COMMON_IMAGE_UPLOAD_FAILED       = [200004, '图片上传失败'];
    public const COMMON_IMAGE_NOT_FOUND           = [200005, '指定图片不存在'];
    public const COMMON_VOTE_TYPE_ERROR           = [200006, '投票类型只能是 up、down 中的一个'];

    /**
     * 用户相关错误，模块编号：1
     */
    public const USER_NEED_LOGIN                  = [201001, '用户未登录'];
    public const USER_NEED_MANAGE_PERMISSION      = [201002, '需要管理员权限'];
    public const USER_NOT_FOUND                   = [201003, '指定用户不存在'];
    public const USER_TARGET_NOT_FOUND            = [201004, '目标用户不存在'];
    public const USER_DISABLED                    = [201005, '该用户已被禁用'];
    public const USER_PASSWORD_ERROR              = [201006, '账号或密码错误'];
    public const USER_AVATAR_UPLOAD_FAILED        = [201007, '头像上传失败'];
    public const USER_COVER_UPLOAD_FAILED         = [201008, '封面上传失败'];
    public const USER_CANT_FOLLOW_YOURSELF        = [201009, '不能关注你自己'];

    /**
     * 提问相关错误，模块编号：2
     */
    public const QUESTION_NOT_FOUND               = [202001, '指定提问不存在'];

    public const QUESTION_CANT_EDIT               = [202002, '提问发表后即无法编辑'];
    public const QUESTION_CANT_EDIT_NOT_AUTHOR    = [202003, '仅提问作者可以编辑提问'];
    public const QUESTION_CANT_EDIT_TIMEOUT       = [202004, '已超过可编辑的时间'];
    public const QUESTION_CANT_EDIT_HAS_ANSWER    = [202005, '该提问下已有回答，不允许编辑'];
    public const QUESTION_CANT_EDIT_HAS_COMMENT   = [202006, '该提问下已有评论，不允许编辑'];

    public const QUESTION_CANT_DELETE             = [202007, '提问发表后即无法删除'];
    public const QUESTION_CANT_DELETE_NOT_AUTHOR  = [202008, '仅提问作者可以删除提问'];
    public const QUESTION_CANT_DELETE_TIMEOUT     = [202009, '已超过可删除的时间'];
    public const QUESTION_CANT_DELETE_HAS_ANSWER  = [202010, '该提问下已有回答，不允许删除'];
    public const QUESTION_CANT_DELETE_HAS_COMMENT = [202011, '该提问下已有评论，不允许删除'];

    /**
     * 回答相关错误，模块编号：3
     */
    public const ANSWER_NOT_FOUND                 = [203001, '指定回答不存在'];

    public const ANSWER_CANT_EDIT                 = [203002, '回答发表后即无法编辑'];
    public const ANSWER_CANT_EDIT_NOT_AUTHOR      = [203003, '仅回答的作者可以编辑回答'];
    public const ANSWER_CANT_EDIT_TIMEOUT         = [203004, '已超过可编辑的时间'];
    public const ANSWER_CANT_EDIT_HAS_COMMENT     = [203005, '该回答下已有评论，不允许编辑'];

    public const ANSWER_CANT_DELETE               = [203006, '回答发表后即无法删除'];
    public const ANSWER_CANT_DELETE_NOT_AUTHOR    = [203007, '仅回答的作者可以删除回答'];
    public const ANSWER_CANT_DELETE_TIMEOUT       = [203008, '已超过可删除的时间'];
    public const ANSWER_CANT_DELETE_HAS_COMMENT   = [203009, '该回答下已有评论，不允许删除'];

    /**
     * 评论相关错误，模块编号：4
     */
    public const COMMENT_NOT_FOUND                = [204001, '指定的评论不存在'];

    public const COMMENT_CANT_EDIT                = [204002, '评论发表后即无法编辑'];
    public const COMMENT_CANT_EDIT_NOT_AUTHOR     = [204003, '仅评论的作者可以编辑评论'];
    public const COMMENT_CANT_EDIT_TIMEOUT        = [204004, '已超过可编辑时间'];

    public const COMMENT_CANT_DELETE              = [204005, '评论发表后即无法删除'];
    public const COMMENT_CANT_DELETE_NOT_AUTHOR   = [204006, '仅评论的作者可以删除评论'];
    public const COMMENT_CANT_DELETE_TIMEOUT      = [204007, '已超过可删除时间'];

    /**
     * 话题相关错误，模块编号：5
     */
    public const TOPIC_NOT_FOUND                  = [205001, '指定话题不存在'];
    public const TOPIC_COVER_UPLOAD_FAILED        = [205002, '话题封面上传失败'];

    /**
     * 文章相关错误，模块编号：6
     */
    public const ARTICLE_NOT_FOUND                = [206001, '指定文章不存在'];

    public const ARTICLE_CANT_EDIT                = [206002, '文章发表后即无法编辑'];
    public const ARTICLE_CANT_EDIT_NOT_AUTHOR     = [206003, '仅文章作者可以编辑文章'];
    public const ARTICLE_CANT_EDIT_TIMEOUT        = [206004, '已超过可编辑时间'];
    public const ARTICLE_CANT_EDIT_HAS_COMMENT    = [206005, '该文章下已有评论，不允许编辑'];

    public const ARTICLE_CANT_DELETE              = [206006, '文章发表后即无法删除'];
    public const ARTICLE_CANT_DELETE_NOT_AUTHOR   = [206007, '仅文章作者可以删除文章'];
    public const ARTICLE_CANT_DELETE_TIMEOUT      = [206008, '已超过可删除时间'];
    public const ARTICLE_CANT_DELETE_HAS_COMMENT  = [206009, '该文章下已有评论，不允许删除'];

    /**
     * 举报相关错误，模块编号：7
     */
    public const REPORT_NOT_FOUND                 = [207001, '指定举报不存在'];
    public const REPORT_TARGET_NOT_FOUND          = [207002, '举报目标不存在'];
    public const REPORT_ALREADY_SUBMITTED         = [207003, '不能重复举报'];
}
