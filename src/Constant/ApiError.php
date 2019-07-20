<?php

declare(strict_types=1);

namespace MDClub\Constant;

/**
 * 错误代码
 */
class ApiError
{
    /**
     * 系统级错误
     */
    public const SYSTEM_ERROR                    = [100000, '服务器错误'];
    public const SYSTEM_MAINTAIN                 = [100001, '系统维护中'];
    public const SYSTEM_IP_LIMIT                 = [100002, 'IP 受限'];
    public const SYSTEM_API_NOT_FOUND            = [100001, '接口不存在'];
    public const SYSTEM_API_NOT_ALLOWED          = [100000, '该接口不支持此 HTTP METHOD'];
    public const SYSTEM_FIELD_VERIFY_FAILED      = [100002, '字段验证失败'];
    public const SYSTEM_SEND_EMAIL_FAILED        = [100003, '邮件发送失败'];
    public const SYSTEM_EMAIL_VERIFY_EXPIRED     = [100004, '邮件验证码已失效'];
    public const SYSTEM_IMAGE_UPLOAD_FAILED      = [100005, '图片上传失败'];
    public const SYSTEM_VOTE_TYPE_ERROR          = [100006, '投票类型只能是 up、down 中的一个'];

    /**
     * 服务级错误
     */
    public const USER_TOKEN_EMPTY                = [200002, 'token 不能为空'];
    public const USER_TOKEN_FAILED               = [200003, 'token 已失效'];
    public const USER_TOKEN_NOT_FOUND            = [200004, 'token 对于的记录不存在'];
    public const USER_NEED_MANAGE_PERMISSION     = [200005, '需要管理员权限'];
    public const USER_NOT_FOUND                  = [200006, '指定用户不存在'];
    public const USER_TARGET_NOT_FOUND           = [200007, '目标用户不存在'];
    public const USER_HAS_BEEN_DISABLED          = [200013, '该用户已被禁用'];
    public const USER_PASSWORD_ERROR             = [200014, '账号或密码错误'];
    public const USER_AVATAR_UPLOAD_FAILED       = [200008, '头像上传失败'];
    public const USER_COVER_UPLOAD_FAILED        = [200009, '封面上传失败'];
    public const USER_CANT_FOLLOW_YOURSELF       = [200012, '不能关注你自己'];

    /**
     * 提问相关错误
     */
    public const QUESTION_NOT_FOUND              = [300001, '指定提问不存在'];

    public const QUESTION_CANT_EDIT_NOT_AUTHOR   = [300002, '仅提问作者可以编辑提问'];
    public const QUESTION_CANT_EDIT              = [300003, '提问发表后即无法编辑'];
    public const QUESTION_CANT_EDIT_TIMEOUT      = [300004, '已超过可编辑的时间'];
    public const QUESTION_CANT_EDIT_HAS_ANSWER   = [300005, '该提问下已有回答，不允许编辑'];
    public const QUESTION_CANT_EDIT_HAS_COMMENT  = [300006, '该提问下已有评论，不允许编辑'];

    public const QUESTION_CANT_DELETE_NOT_AUTHOR = [300007, '仅提问作者可以删除提问'];
    public const QUESTION_CANT_DELETE            = [300008, '提问发表后即无法删除'];
    public const QUESTION_CANT_DELETE_TIMEOUT    = [300009, '已超过可删除的时间'];
    public const QUESTION_CANT_DELETE_HAS_ANSWER = [300010, '该提问下已有回答，不允许删除'];
    public const QUESTION_CANT_DELETE_HAS_COMMENT= [300011, '该提问下已有评论，不允许删除'];

    /**
     * 回答相关错误
     */
    public const ANSWER_NOT_FOUND                = [310001, '指定回答不存在'];

    public const ANSWER_CANT_EDIT_NOT_AUTHOR     = [310002, '仅回答的作者可以编辑回答'];
    public const ANSWER_CANT_EDIT                = [310004, '回答发表后即无法编辑'];
    public const ANSWER_CANT_EDIT_TIMEOUT        = [310006, '已超过可编辑的时间'];
    public const ANSWER_CANT_EDIT_HAS_COMMENT    = [310008, '该回答下已有评论，不允许编辑'];

    public const ANSWER_CANT_DELETE_NOT_AUTHOR   = [310003, '仅回答的作者可以删除回答'];
    public const ANSWER_CANT_DELETE              = [310005, '回答发表后即无法删除'];
    public const ANSWER_CANT_DELETE_TIMEOUT      = [310007, '已超过可删除的时间'];
    public const ANSWER_CANT_DELETE_HAS_COMMENT  = [310009, '该回答下已有评论，不允许删除'];

    /**
     * 评论相关错误
     */
    public const COMMENT_NOT_FOUND               = [320001, '指定的评论不存在'];

    public const COMMENT_CANT_EDIT_NOT_AUTHOR    = [320002, '仅评论的作者可以编辑评论'];
    public const COMMENT_CANT_EDIT               = [320004, '评论发表后即无法编辑'];
    public const COMMENT_CANT_EDIT_TIMEOUT       = [320005, '已超过可编辑时间'];

    public const COMMENT_CANT_DELETE_NOT_AUTHOR  = [320003, '仅评论的作者可以删除评论'];
    public const COMMENT_CANT_DELETE             = [320006, '评论发表后即无法删除'];
    public const COMMENT_CANT_DELETE_TIMEOUT     = [320007, '已超过可删除时间'];

    /**
     * 话题相关错误
     */
    public const TOPIC_NOT_FOUND                 = [400001, '指定话题不存在'];
    public const TOPIC_COVER_UPLOAD_FAILED       = [400002, '话题封面上传失败'];

    /**
     * 文章相关错误
     */
    public const ARTICLE_NOT_FOUND               = [500001, '指定文章不存在'];

    public const ARTICLE_CANT_EDIT_NOT_AUTHOR    = [500002, '仅文章作者可以编辑文章'];
    public const ARTICLE_CANT_EDIT               = [500003, '文章发表后即无法编辑'];
    public const ARTICLE_CANT_EDIT_TIMEOUT       = [500004, '已超过可编辑时间'];
    public const ARTICLE_CANT_EDIT_HAS_COMMENT   = [500005, '该文章下已有评论，不允许编辑'];

    public const ARTICLE_CANT_DELETE_NOT_AUTHOR  = [500006, '仅文章作者可以删除文章'];
    public const ARTICLE_CANT_DELETE             = [500007, '文章发表后即无法删除'];
    public const ARTICLE_CANT_DELETE_TIMEOUT     = [500008, '已超过可删除时间'];
    public const ARTICLE_CANT_DELETE_HAS_COMMENT = [500009, '该文章下已有评论，不允许删除'];

    /**
     * 举报相关错误
     */
    public const REPORT_NOT_FOUND                = [600001, '指定举报不存在'];
    public const REPORT_ALREADY_SUBMITTED        = [600002, '不能重复举报'];

    /**
     * 图片相关错误
     */
    public const IMAGE_NOT_FOUND                 = [700001, '指定图片不存在'];
}
