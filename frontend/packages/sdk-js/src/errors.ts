/**
 * 错误代码
 *
 * 错误码格式：ABBCCC
 * A：错误级别，1：系统级错误；2：服务级错误
 * B：模块编号
 * C：具体错误编号
 */

/**
 * =================================================================== 系统级错误
 */

/**
 * 服务器错误
 */
export const SYSTEM_ERROR = 100000;

/**
 * 系统维护中
 */
export const SYSTEM_MAINTAIN = 100001;

/**
 * IP 请求超过上限
 */
export const SYSTEM_IP_LIMIT = 100002;

/**
 * 用户请求超过上限
 */
export const SYSTEM_USER_LIMIT = 100003;

/**
 * 接口不存在
 */
export const SYSTEM_API_NOT_FOUND = 100004;

/**
 * 该接口不支持此 HTTP METHOD
 */
export const SYSTEM_API_NOT_ALLOWED = 100005;

/**
 * 请求参数的 json 格式错误
 */
export const SYSTEM_REQUEST_JSON_INVALID = 100006;

/**
 * 系统安装失败
 */
export const SYSTEM_INSTALL_FAILED = 100007;

/**
 * ===================================================== 通用服务错误，模块编号：0
 */

/**
 * 字段验证失败
 */
export const COMMON_FIELD_VERIFY_FAILED = 200001;

/**
 * 邮件发送失败
 */
export const COMMON_SEND_EMAIL_FAILED = 200002;

/**
 * 邮件验证码已失效
 */
export const COMMON_EMAIL_VERIFY_EXPIRED = 200003;

/**
 * 图片上传失败
 */
export const COMMON_IMAGE_UPLOAD_FAILED = 200004;

/**
 * 指定图片不存在
 */
export const COMMON_IMAGE_NOT_FOUND = 200005;

/**
 * 投票类型只能是 up、down 中的一个
 */
export const COMMON_VOTE_TYPE_ERROR = 200006;

/**
 * ===================================================== 用户相关错误，模块编号：1
 */

/**
 * 用户未登录
 */
export const USER_NEED_LOGIN = 201001;

/**
 * 需要管理员权限
 */
export const USER_NEED_MANAGE_PERMISSION = 201002;

/**
 * 指定用户不存在
 */
export const USER_NOT_FOUND = 201003;

/**
 * 目标用户不存在
 */
export const USER_TARGET_NOT_FOUND = 201004;

/**
 * 该用户已被禁用
 */
export const USER_DISABLED = 201005;

/**
 * 账号或密码错误
 */
export const USER_PASSWORD_ERROR = 201006;

/**
 * 头像上传失败
 */
export const USER_AVATAR_UPLOAD_FAILED = 201007;

/**
 * 封面上传失败
 */
export const USER_COVER_UPLOAD_FAILED = 201008;

/**
 * 不能关注你自己
 */
export const USER_CANT_FOLLOW_YOURSELF = 201009;

/**
 * ===================================================== 提问相关错误，模块编号：2
 */

/**
 * 指定提问不存在
 */
export const QUESTION_NOT_FOUND = 202001;

/**
 * 提问发表后即无法编辑
 */
export const QUESTION_CANT_EDIT = 202002;

/**
 * 仅提问作者可以编辑提问
 */
export const QUESTION_CANT_EDIT_NOT_AUTHOR = 202003;

/**
 * 已超过可编辑的时间
 */
export const QUESTION_CANT_EDIT_TIMEOUT = 202004;

/**
 * 该提问下已有回答，不允许编辑
 */
export const QUESTION_CANT_EDIT_HAS_ANSWER = 202005;

/**
 * 该提问下已有评论，不允许编辑
 */
export const QUESTION_CANT_EDIT_HAS_COMMENT = 202006;

/**
 * 提问发表后即无法删除
 */
export const QUESTION_CANT_DELETE = 202007;

/**
 * 仅提问作者可以删除提问
 */
export const QUESTION_CANT_DELETE_NOT_AUTHOR = 202008;

/**
 * 已超过可删除的时间
 */
export const QUESTION_CANT_DELETE_TIMEOUT = 202009;

/**
 * 该提问下已有回答，不允许删除
 */
export const QUESTION_CANT_DELETE_HAS_ANSWER = 202010;

/**
 * 该提问下已有评论，不允许删除
 */
export const QUESTION_CANT_DELETE_HAS_COMMENT = 202011;

/**
 * ===================================================== 回答相关错误，模块编号：3
 */

/**
 * 指定回答不存在
 */
export const ANSWER_NOT_FOUND = 203001;

/**
 * 回答发表后即无法编辑
 */
export const ANSWER_CANT_EDIT = 203002;

/**
 * 仅回答的作者可以编辑回答
 */
export const ANSWER_CANT_EDIT_NOT_AUTHOR = 203003;

/**
 * 已超过可编辑的时间
 */
export const ANSWER_CANT_EDIT_TIMEOUT = 203004;

/**
 * 该回答下已有评论，不允许编辑
 */
export const ANSWER_CANT_EDIT_HAS_COMMENT = 203005;

/**
 * 回答发表后即无法删除
 */
export const ANSWER_CANT_DELETE = 203006;

/**
 * 仅回答的作者可以删除回答
 */
export const ANSWER_CANT_DELETE_NOT_AUTHOR = 203007;

/**
 * 已超过可删除的时间
 */
export const ANSWER_CANT_DELETE_TIMEOUT = 203008;

/**
 * 该回答下已有评论，不允许删除
 */
export const ANSWER_CANT_DELETE_HAS_COMMENT = 203009;

/**
 * ===================================================== 评论相关错误，模块编号：4
 */

/**
 * 指定的评论不存在
 */
export const COMMENT_NOT_FOUND = 204001;

/**
 * 评论发表后即无法编辑
 */
export const COMMENT_CANT_EDIT = 204002;

/**
 * 仅评论的作者可以编辑评论
 */
export const COMMENT_CANT_EDIT_NOT_AUTHOR = 204003;

/**
 * 已超过可编辑时间
 */
export const COMMENT_CANT_EDIT_TIMEOUT = 204004;

/**
 * 评论发表后即无法删除
 */
export const COMMENT_CANT_DELETE = 204005;

/**
 * 仅评论的作者可以删除评论
 */
export const COMMENT_CANT_DELETE_NOT_AUTHOR = 204006;

/**
 * 已超过可删除时间
 */
export const COMMENT_CANT_DELETE_TIMEOUT = 204007;

/**
 * ===================================================== 话题相关错误，模块编号：5
 */

/**
 * 指定话题不存在
 */
export const TOPIC_NOT_FOUND = 205001;

/**
 * 话题封面上传失败
 */
export const TOPIC_COVER_UPLOAD_FAILED = 205002;

/**
 * ===================================================== 文章相关错误，模块编号：6
 */

/**
 * 指定文章不存在
 */
export const ARTICLE_NOT_FOUND = 206001;

/**
 * 文章发表后即无法编辑
 */
export const ARTICLE_CANT_EDIT_NOT_AUTHOR = 206002;

/**
 * 仅文章作者可以编辑文章
 */
export const ARTICLE_CANT_EDIT = 206003;

/**
 * 已超过可编辑时间
 */
export const ARTICLE_CANT_EDIT_TIMEOUT = 206004;

/**
 * 该文章下已有评论，不允许编辑
 */
export const ARTICLE_CANT_EDIT_HAS_COMMENT = 206005;

/**
 * 文章发表后即无法删除
 */
export const ARTICLE_CANT_DELETE_NOT_AUTHOR = 206006;

/**
 * 仅文章作者可以删除文章
 */
export const ARTICLE_CANT_DELETE = 206007;

/**
 * 已超过可删除时间
 */
export const ARTICLE_CANT_DELETE_TIMEOUT = 206008;

/**
 * 该文章下已有评论，不允许删除
 */
export const ARTICLE_CANT_DELETE_HAS_COMMENT = 206009;

/**
 * ===================================================== 举报相关错误，模块编号：7
 */

/**
 * 指定举报不存在
 */
export const REPORT_NOT_FOUND = 207001;

/**
 * 举报目标不存在
 */
export const REPORT_TARGET_NOT_FOUND = 207002;

/**
 * 不能重复举报
 */
export const REPORT_ALREADY_SUBMITTED = 207003;

/**
 * ===================================================== 通知相关错误，模块编号：8
 */

/**
 * 指定通知不存在
 */
export const NOTIFICATION_NOT_FOUND = 208001;
