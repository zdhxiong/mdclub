import PlainObject from 'mdui.jq/es/interfaces/PlainObject';

export interface Answer {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * markdown 格式的内容
   */
  content_markdown: string;
  /**
   * html 格式的内容
   */
  content_rendered: string;
  /**
   * 评论数量
   */
  comment_count: number;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  vote_count: number;
  /**
   * 赞成票总数
   */
  vote_up_count: number;
  /**
   * 反对票总数
   */
  vote_down_count: number;
  /**
   * 创建时间
   */
  create_time: number;
  /**
   * 更新时间
   */
  update_time: number;
  /**
   * 🔐删除时间
   */
  delete_time?: number;
  relationships?: AnswerRelationship;
}

export interface AnswerInRelationship {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 内容摘要
   */
  content_summary: string;
  /**
   * 发布时间
   */
  create_time: number;
  /**
   * 更新时间
   */
  update_time: number;
}

export interface AnswerRelationship {
  user?: UserInRelationship;
  question?: QuestionInRelationship;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: AnswerRelationshipVotingEnum;
}

/**
 * Enum for the voting property.
 */
export type AnswerRelationshipVotingEnum = 'up' | 'down' | '';

export interface AnswerRequestBody {
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

export interface AnswerResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Answer;
}

export interface AnswersResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Answer>;
  pagination: Pagination;
}

export interface Article {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 文章标题
   */
  title: string;
  /**
   * Markdown 格式的文章内容
   */
  content_markdown: string;
  /**
   * HTML 格式的文章内容
   */
  content_rendered: string;
  /**
   * 评论数量
   */
  comment_count: number;
  /**
   * 关注者数量
   */
  follower_count: number;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  vote_count: number;
  /**
   * 赞成票总数
   */
  vote_up_count: number;
  /**
   * 反对票总数
   */
  vote_down_count: number;
  /**
   * 创建时间
   */
  create_time: number;
  /**
   * 更新时间（用户可以更新自己的文章）
   */
  update_time: number;
  /**
   * 🔐删除时间
   */
  delete_time?: number;
  relationships?: ArticleRelationship;
}

export interface ArticleCreateRequestBody {
  /**
   * 标题
   */
  title: string;
  /**
   * 话题ID
   */
  topic_ids: Array<number>;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

export interface ArticleInRelationship {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 文章标题
   */
  title: string;
  /**
   * 发布时间
   */
  create_time: number;
  /**
   * 更新时间
   */
  update_time: number;
}

export interface ArticleRelationship {
  user?: UserInRelationship;
  topics?: Array<TopicInRelationship>;
  /**
   * 当前登录用户是否已关注该文章
   */
  is_following?: boolean;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: ArticleRelationshipVotingEnum;
}

/**
 * Enum for the voting property.
 */
export type ArticleRelationshipVotingEnum = 'up' | 'down' | '';

export interface ArticleResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Article;
}

export interface ArticleUpdateRequestBody {
  /**
   * 标题
   */
  title?: string;
  /**
   * 话题ID
   */
  topic_ids?: Array<number>;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

export interface ArticlesResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Article>;
  pagination: Pagination;
}

export interface Captcha {
  /**
   * 图形验证码token
   */
  captcha_token: string;
  /**
   * base64格式的图形验证码图片
   */
  captcha_image: string;
}

export interface CaptchaResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Captcha;
}

export interface Comment {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 评论目标的ID
   */
  commentable_id: number;
  /**
   * 评论目标类型
   */
  commentable_type: CommentCommentableTypeEnum;
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 评论内容
   */
  content: string;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  vote_count: number;
  /**
   * 赞成票总数
   */
  vote_up_count: number;
  /**
   * 反对票总数
   */
  vote_down_count: number;
  /**
   * 发表时间
   */
  create_time: number;
  /**
   * 修改时间
   */
  update_time: number;
  /**
   * 🔐删除时间
   */
  delete_time?: number;
  relationships?: CommentRelationship;
}

/**
 * Enum for the commentable_type property.
 */
export type CommentCommentableTypeEnum =
  | 'article'
  | 'question'
  | 'answer'
  | 'comment';

export interface CommentInRelationship {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 内容摘要
   */
  content_summary: string;
  /**
   * 发布时间
   */
  create_time: number;
  /**
   * 更新时间
   */
  update_time: number;
}

export interface CommentRelationship {
  user?: UserInRelationship;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: CommentRelationshipVotingEnum;
}

/**
 * Enum for the voting property.
 */
export type CommentRelationshipVotingEnum = 'up' | 'down' | '';

export interface CommentRequestBody {
  /**
   * 评论内容
   */
  content: string;
}

export interface CommentResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Comment;
}

export interface CommentsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Comment>;
  pagination: Pagination;
}

export interface Email {
  /**
   * 邮箱地址
   */
  email: Array<string>;
  /**
   * 邮件标题
   */
  subject: string;
  /**
   * 邮件内容
   */
  content: string;
}

export interface EmailResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Email;
}

export interface EmptyResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: PlainObject;
}

export interface ErrorResponse {
  /**
   * 错误代码
   */
  code: number;
  /**
   * 错误描述
   */
  message: string;
  /**
   * 额外的错误描述
   */
  extra_message?: string;
  /**
   * 图形验证码token。若返回了该参数，表示下次调用该接口需要输入图形验证码
   */
  captcha_token?: string;
  /**
   * 图形验证码的base64格式图片
   */
  captcha_image?: string;
  /**
   * 错误字段。键名为字段名，键值为错误信息
   */
  errors?: { [key: string]: string };
}

/**
 * 关注者数量
 */
export interface FollowerCount {
  /**
   * 关注者数量
   */
  follower_count: number;
}

export interface FollowerCountResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: FollowerCount;
}

export interface Image {
  /**
   * 图片的 key
   */
  key: string;
  /**
   * 图片原始文件名
   */
  filename: string;
  /**
   * 原始图片宽度
   */
  width: number;
  /**
   * 原始图片高度
   */
  height: number;
  /**
   * 图片上传时间
   */
  create_time: number;
  /**
   * 图片关联对象类型
   */
  item_type: ImageItemTypeEnum;
  /**
   * 图片关联对象ID
   */
  item_id: number;
  /**
   * 图片上传者ID
   */
  user_id: number;
  urls: ImageUrls;
  relationships?: ImageRelationship;
}

/**
 * Enum for the item_type property.
 */
export type ImageItemTypeEnum = 'question' | 'answer' | 'article';

export interface ImageRelationship {
  user?: UserInRelationship;
  question?: QuestionInRelationship;
  article?: ArticleInRelationship;
  answer?: AnswerInRelationship;
}

export interface ImageResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Image;
}

export interface ImageUpdateRequestBody {
  /**
   * 图片文件名
   */
  filename: string;
}

export interface ImageUploadRequestBody {
  /**
   * 图片
   */
  image: any;
}

export interface ImageUrls {
  /**
   * 原图地址
   */
  original: string;
  /**
   * 宽度固定，高度自适应的图片地址
   */
  release: string;
  /**
   * 固定宽高的缩略图地址
   */
  thumb: string;
}

export interface ImagesResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Image>;
  pagination: Pagination;
}

export interface Notification {
  /**
   * 通知ID
   */
  notification_id: number;
  /**
   * 通知接收者ID
   */
  receiver_id: number;
  /**
   * 通知发送者ID
   */
  sender_id: number;
  /**
   * 通知类型
   */
  type: NotificationTypeEnum;
  /**
   * 相关文章ID
   */
  article_id: number;
  /**
   * 相关提问ID
   */
  question_id: number;
  /**
   * 相关回答ID
   */
  answer_id: number;
  /**
   * 相关评论ID
   */
  comment_id: number;
  /**
   * 相关回复ID
   */
  reply_id: number;
  /**
   * 被删除的内容的备份
   */
  content_deleted: string;
  /**
   * 通知发送时间
   */
  create_time: number;
  /**
   * 通知阅读时间
   */
  read_time: number;
  relationships?: NotificationRelationship;
}

/**
 * Enum for the type property.
 */
export type NotificationTypeEnum =
  | 'question_answered'
  | 'question_commented'
  | 'question_deleted'
  | 'article_commented'
  | 'article_deleted'
  | 'answer_commented'
  | 'answer_deleted'
  | 'comment_replied'
  | 'comment_deleted';

/**
 * 未读通知数量
 */
export interface NotificationCount {
  /**
   * 未读通知数量
   */
  notification_count: number;
}

export interface NotificationCountResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: NotificationCount;
}

export interface NotificationRelationship {
  receiver?: UserInRelationship;
  sender?: UserInRelationship;
  article?: ArticleInRelationship;
  question?: QuestionInRelationship;
  answer?: AnswerInRelationship;
  comment?: CommentInRelationship;
  reply?: CommentInRelationship;
}

export interface NotificationResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Notification;
}

export interface NotificationsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Notification>;
  pagination: Pagination;
}

export interface Option {
  /**
   * 回答作者是否可删除回答。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `answer_can_delete_before` 和 `answer_can_delete_only_no_comment` 的条件时可删除。
   */
  answer_can_delete: boolean;
  /**
   * 在发表后多少秒内，允许作者删除回答（为 `0` 时表示不做限制）。仅在 `answer_can_delete` 为 `true` 时该参数才有效。
   */
  answer_can_delete_before: number;
  /**
   * 是否仅在没有评论时，允许作者删除回答。仅在 `answer_can_delete` 为 `true` 时该参数才有效。
   */
  answer_can_delete_only_no_comment: boolean;
  /**
   * 回答作者是否可编辑回答。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `answer_can_edit_before` 和 `answer_can_edit_only_no_comment` 的条件时可编辑。
   */
  answer_can_edit: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑回答（为 `0` 时表示不做限制）。仅在 `answer_can_edit` 为 `true` 时该参数才有效。
   */
  answer_can_edit_before: number;
  /**
   * 是否仅在没有评论时，允许作者编辑回答。仅在 `answer_can_edit` 为 `true` 时该参数才有效。
   */
  answer_can_edit_only_no_comment: boolean;
  /**
   * 文章作者是否可删除文章。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `article_can_delete_before` 和 `article_can_delete_only_no_comment` 的条件时可删除。
   */
  article_can_delete: boolean;
  /**
   * 在发表后多少秒内，允许作者删除文章（为 `0` 时表示不做限制）。仅在 `article_can_delete` 为 `true` 时该参数才有效。
   */
  article_can_delete_before: number;
  /**
   * 仅在没有评论时，允许作者删除文章。仅在 `article_can_delete` 为 `true` 时该参数才有效。
   */
  article_can_delete_only_no_comment: boolean;
  /**
   * 文章作者是否可编辑文章。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `article_can_edit_before` 和 `article_can_edit_only_no_comment` 的条件时可编辑。
   */
  article_can_edit: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑文章（为 `0` 时表示不做限制）。仅在 `article_can_edit` 为 `true` 时该参数才有效。
   */
  article_can_edit_before: number;
  /**
   * 仅在没有评论时，允许作者编辑文章。仅在 `article_can_edit` 为 `true` 时该参数才有效。
   */
  article_can_edit_only_no_comment: boolean;
  /**
   * 🔐Memcached 服务器地址
   */
  cache_memcached_host?: string;
  /**
   * 🔐Memcached 密码
   */
  cache_memcached_password?: string;
  /**
   * 🔐Memcached 端口号
   */
  cache_memcached_port?: number;
  /**
   * 🔐Memcached 用户名
   */
  cache_memcached_username?: string;
  /**
   * 🔐缓存键名前缀（只能包含字符 -+.A-Za-z0-9）
   */
  cache_prefix?: string;
  /**
   * 🔐Redis 服务器地址
   */
  cache_redis_host?: string;
  /**
   * 🔐Redis 密码
   */
  cache_redis_password?: string;
  /**
   * 🔐Redis 端口号
   */
  cache_redis_port?: number;
  /**
   * 🔐Redis 用户名
   */
  cache_redis_username?: string;
  /**
   * 🔐缓存类型
   */
  cache_type?: OptionCacheTypeEnum;
  /**
   * 评论作者是否可删除评论。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `comment_can_delete_before` 的条件时可删除。
   */
  comment_can_delete: boolean;
  /**
   * 在发表后多少秒内，允许作者删除评论（为 `0` 时表示不做限制）。仅在 `comment_can_delete` 为 `true` 时该参数才有效。
   */
  comment_can_delete_before: number;
  /**
   * 评论作者是否可编辑评论。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `comment_can_edit_before` 的条件时可编辑。
   */
  comment_can_edit: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑评论（为 `0` 时表示不做限制）。仅在 `comment_can_edit` 为 `true` 时该参数才有效。
   */
  comment_can_edit_before: number;
  /**
   * 系统语言
   */
  language: OptionLanguageEnum;
  /**
   * 提问作者是否可删除提问。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `question_can_delete_before`、`question_can_delete_only_no_answer` 和 `question_can_delete_only_no_comment` 的条件时可删除。
   */
  question_can_delete: boolean;
  /**
   * 在发表后多少秒内，允许作者删除提问（为 `0` 时表示不做限制）。仅在 `question_can_delete` 为 `true` 时该参数才有效。
   */
  question_can_delete_before: number;
  /**
   * 仅在没有回答时，允许作者删除提问。仅在 `question_can_delete` 为 `true` 时该参数才有效。
   */
  question_can_delete_only_no_answer: boolean;
  /**
   * 仅在没有评论时，允许作者删除提问。仅在 `question_can_delete` 为 `true` 时该参数才有效。
   */
  question_can_delete_only_no_comment: boolean;
  /**
   * 提问作者是否可编辑提问。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `question_can_edit_before`、`question_can_edit_only_no_answer` 和 `question_can_edit_only_no_comment` 的条件时可编辑。
   */
  question_can_edit: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑提问（为 `0` 时表示不做限制）。仅在 `question_can_edit` 为 `true` 时该参数才有效。
   */
  question_can_edit_before: number;
  /**
   * 仅在没有回答时，允许作者编辑提问。仅在 `question_can_edit` 为 `true` 时该参数才有效。
   */
  question_can_edit_only_no_answer: boolean;
  /**
   * 仅在没有评论时，允许作者编辑提问。仅在 `question_can_edit` 为 `true` 时该参数才有效。
   */
  question_can_edit_only_no_comment: boolean;
  /**
   * 第三方搜索的名称
   */
  search_third: OptionSearchThirdEnum;
  /**
   * 搜索引擎类型
   */
  search_type: OptionSearchTypeEnum;
  /**
   * 站点简介
   */
  site_description: string;
  /**
   * 站点公安备案号
   */
  site_gongan_beian: string;
  /**
   * 站点 ICP 备案号
   */
  site_icp_beian: string;
  /**
   * 站点关键词
   */
  site_keywords: string;
  /**
   * 站点名称
   */
  site_name: string;
  /**
   * 🔐静态资源 URL 地址
   */
  site_static_url?: string;
  /**
   * 🔐SMTP 服务器地址
   */
  smtp_host?: string;
  /**
   * 🔐SMTP 密码
   */
  smtp_password?: string;
  /**
   * 🔐SMTP 端口
   */
  smtp_port?: number;
  /**
   * 🔐SMTP 回信地址
   */
  smtp_reply_to?: string;
  /**
   * 🔐SMTP 加密方式
   */
  smtp_secure?: OptionSmtpSecureEnum;
  /**
   * 🔐SMTP 账户
   */
  smtp_username?: string;
  /**
   * 🔐阿里云 AccessKey ID
   */
  storage_aliyun_access_id?: string;
  /**
   * 🔐阿里云 Access Key Secret
   */
  storage_aliyun_access_secret?: string;
  /**
   * 🔐阿里云 OSS 的 Bucket 名称
   */
  storage_aliyun_bucket?: string;
  /**
   * 🔐阿里云 OSS 的存储子目录
   */
  storage_aliyun_dir?: string;
  /**
   * 🔐阿里云 OSS 的 EndPoint
   */
  storage_aliyun_endpoint?: string;
  /**
   * 🔐FTP 服务器地址
   */
  storage_ftp_host?: string;
  /**
   * 🔐是否使用被动传输模式。true（被动模式）；false（主动模式）
   */
  storage_ftp_passive?: boolean;
  /**
   * 🔐FTP 密码
   */
  storage_ftp_password?: string;
  /**
   * 🔐FTP 端口号
   */
  storage_ftp_port?: number;
  /**
   * 🔐FTP 存储目录
   */
  storage_ftp_dir?: string;
  /**
   * 🔐FTP 是否启用 SSL。true（启用）；false（不启用）
   */
  storage_ftp_ssl?: boolean;
  /**
   * 🔐FTP 用户名
   */
  storage_ftp_username?: string;
  /**
   * 🔐本地文件存储目录
   */
  storage_local_dir?: string;
  /**
   * 🔐七牛云 AccessKey
   */
  storage_qiniu_access_id?: string;
  /**
   * 🔐七牛云 SecretKey
   */
  storage_qiniu_access_secret?: string;
  /**
   * 🔐七牛云 Bucket
   */
  storage_qiniu_bucket?: string;
  /**
   * 🔐七牛云的存储子目录
   */
  storage_qiniu_dir?: string;
  /**
   * 🔐FTP 存储区域。z0（华东）；z1（华北）；z2（华南）；na0（北美）；as0（东南亚）
   */
  storage_qiniu_zone?: OptionStorageQiniuZoneEnum;
  /**
   * 🔐SFTP 服务器地址
   */
  storage_sftp_host?: string;
  /**
   * 🔐SFTP 密码
   */
  storage_sftp_password?: string;
  /**
   * 🔐SFTP 端口号
   */
  storage_sftp_port?: number;
  /**
   * 🔐SFTP 存储目录
   */
  storage_sftp_dir?: string;
  /**
   * 🔐SFTP 用户名
   */
  storage_sftp_username?: string;
  /**
   * 🔐存储类型
   */
  storage_type?: OptionStorageTypeEnum;
  /**
   * 🔐又拍云 Bucket
   */
  storage_upyun_bucket?: string;
  /**
   * 🔐又拍云的存储子目录
   */
  storage_upyun_dir?: string;
  /**
   * 🔐又拍云操作员账号
   */
  storage_upyun_operator?: string;
  /**
   * 🔐又拍云操作员密码
   */
  storage_upyun_password?: string;
  /**
   * 🔐本地文件访问链接
   */
  storage_url?: string;
  /**
   * 🔐主题名称
   */
  theme?: string;
}

/**
 * Enum for the cache_type property.
 */
export type OptionCacheTypeEnum = 'pdo' | 'redis' | 'memcached';

/**
 * Enum for the language property.
 */
export type OptionLanguageEnum = 'en' | 'pl' | 'ru' | 'zh-CN' | 'zh-TW';

/**
 * Enum for the search_third property.
 */
export type OptionSearchThirdEnum =
  | 'google'
  | 'bing'
  | 'sogou'
  | '360'
  | 'baidu';

/**
 * Enum for the search_type property.
 */
export type OptionSearchTypeEnum = 'third';

/**
 * Enum for the smtp_secure property.
 */
export type OptionSmtpSecureEnum = 'ssl' | 'tls' | '';

/**
 * Enum for the storage_qiniu_zone property.
 */
export type OptionStorageQiniuZoneEnum = 'z0' | 'z1' | 'z2' | 'na0' | 'as0';

/**
 * Enum for the storage_type property.
 */
export type OptionStorageTypeEnum =
  | 'local'
  | 'ftp'
  | 'sftp'
  | 'aliyun'
  | 'upyun'
  | 'qiniu';

export interface OptionResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Option;
}

export interface OptionUpdateRequestBody {
  /**
   * 回答作者是否可删除回答。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `answer_can_delete_before` 和 `answer_can_delete_only_no_comment` 的条件时可删除。
   */
  answer_can_delete?: boolean;
  /**
   * 在发表后多少秒内，允许作者删除回答（为 `0` 时表示不做限制）。仅在 `answer_can_delete` 为 `true` 时该参数才有效。
   */
  answer_can_delete_before?: number;
  /**
   * 是否仅在没有评论时，允许作者删除回答。仅在 `answer_can_delete` 为 `true` 时该参数才有效。
   */
  answer_can_delete_only_no_comment?: boolean;
  /**
   * 回答作者是否可编辑回答。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `answer_can_edit_before` 和 `answer_can_edit_only_no_comment` 的条件时可编辑。
   */
  answer_can_edit?: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑回答（为 `0` 时表示不做限制）。仅在 `answer_can_edit` 为 `true` 时该参数才有效。
   */
  answer_can_edit_before?: number;
  /**
   * 是否仅在没有评论时，允许作者编辑回答。仅在 `answer_can_edit` 为 `true` 时该参数才有效。
   */
  answer_can_edit_only_no_comment?: boolean;
  /**
   * 文章作者是否可删除文章。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `article_can_delete_before` 和 `article_can_delete_only_no_comment` 的条件时可删除。
   */
  article_can_delete?: boolean;
  /**
   * 在发表后多少秒内，允许作者删除文章（为 `0` 时表示不做限制）。仅在 `article_can_delete` 为 `true` 时该参数才有效。
   */
  article_can_delete_before?: number;
  /**
   * 仅在没有评论时，允许作者删除文章。仅在 `article_can_delete` 为 `true` 时该参数才有效。
   */
  article_can_delete_only_no_comment?: boolean;
  /**
   * 文章作者是否可编辑文章。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `article_can_edit_before` 和 `article_can_edit_only_no_comment` 的条件时可编辑。
   */
  article_can_edit?: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑文章（为 `0` 时表示不做限制）。仅在 `article_can_edit` 为 `true` 时该参数才有效。
   */
  article_can_edit_before?: number;
  /**
   * 仅在没有评论时，允许作者编辑文章。仅在 `article_can_edit` 为 `true` 时该参数才有效。
   */
  article_can_edit_only_no_comment?: boolean;
  /**
   * 🔐Memcached 服务器地址
   */
  cache_memcached_host?: string;
  /**
   * 🔐Memcached 密码
   */
  cache_memcached_password?: string;
  /**
   * 🔐Memcached 端口号
   */
  cache_memcached_port?: number;
  /**
   * 🔐Memcached 用户名
   */
  cache_memcached_username?: string;
  /**
   * 🔐缓存键名前缀（只能包含字符 -+.A-Za-z0-9）
   */
  cache_prefix?: string;
  /**
   * 🔐Redis 服务器地址
   */
  cache_redis_host?: string;
  /**
   * 🔐Redis 密码
   */
  cache_redis_password?: string;
  /**
   * 🔐Redis 端口号
   */
  cache_redis_port?: number;
  /**
   * 🔐Redis 用户名
   */
  cache_redis_username?: string;
  /**
   * 🔐缓存类型
   */
  cache_type?: OptionCacheTypeEnum;
  /**
   * 评论作者是否可删除评论。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `comment_can_delete_before` 的条件时可删除。
   */
  comment_can_delete?: boolean;
  /**
   * 在发表后多少秒内，允许作者删除评论（为 `0` 时表示不做限制）。仅在 `comment_can_delete` 为 `true` 时该参数才有效。
   */
  comment_can_delete_before?: number;
  /**
   * 评论作者是否可编辑评论。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `comment_can_edit_before` 的条件时可编辑。
   */
  comment_can_edit?: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑评论（为 `0` 时表示不做限制）。仅在 `comment_can_edit` 为 `true` 时该参数才有效。
   */
  comment_can_edit_before?: number;
  /**
   * 系统语言
   */
  language?: OptionLanguageEnum;
  /**
   * 提问作者是否可删除提问。  为 `false` 时，不允许删除； 为 `true` 时，在满足 `question_can_delete_before`、`question_can_delete_only_no_answer` 和 `question_can_delete_only_no_comment` 的条件时可删除。
   */
  question_can_delete?: boolean;
  /**
   * 在发表后多少秒内，允许作者删除提问（为 `0` 时表示不做限制）。仅在 `question_can_delete` 为 `true` 时该参数才有效。
   */
  question_can_delete_before?: number;
  /**
   * 仅在没有回答时，允许作者删除提问。仅在 `question_can_delete` 为 `true` 时该参数才有效。
   */
  question_can_delete_only_no_answer?: boolean;
  /**
   * 仅在没有评论时，允许作者删除提问。仅在 `question_can_delete` 为 `true` 时该参数才有效。
   */
  question_can_delete_only_no_comment?: boolean;
  /**
   * 提问作者是否可编辑提问。  为 `false` 时，不允许编辑； 为 `true` 时，在满足 `question_can_edit_before`、`question_can_edit_only_no_answer` 和 `question_can_edit_only_no_comment` 的条件时可编辑。
   */
  question_can_edit?: boolean;
  /**
   * 在发表后的多少秒内，允许作者编辑提问（为 `0` 时表示不做限制）。仅在 `question_can_edit` 为 `true` 时该参数才有效。
   */
  question_can_edit_before?: number;
  /**
   * 仅在没有回答时，允许作者编辑提问。仅在 `question_can_edit` 为 `true` 时该参数才有效。
   */
  question_can_edit_only_no_answer?: boolean;
  /**
   * 仅在没有评论时，允许作者编辑提问。仅在 `question_can_edit` 为 `true` 时该参数才有效。
   */
  question_can_edit_only_no_comment?: boolean;
  /**
   * 第三方搜索的名称
   */
  search_third?: OptionSearchThirdEnum;
  /**
   * 搜索引擎类型
   */
  search_type?: OptionSearchTypeEnum;
  /**
   * 站点简介
   */
  site_description?: string;
  /**
   * 站点公安备案号
   */
  site_gongan_beian?: string;
  /**
   * 站点 ICP 备案号
   */
  site_icp_beian?: string;
  /**
   * 站点关键词
   */
  site_keywords?: string;
  /**
   * 站点名称
   */
  site_name?: string;
  /**
   * 🔐静态资源 URL 地址
   */
  site_static_url?: string;
  /**
   * 🔐SMTP 服务器地址
   */
  smtp_host?: string;
  /**
   * 🔐SMTP 密码
   */
  smtp_password?: string;
  /**
   * 🔐SMTP 端口
   */
  smtp_port?: number;
  /**
   * 🔐SMTP 回信地址
   */
  smtp_reply_to?: string;
  /**
   * 🔐SMTP 加密方式
   */
  smtp_secure?: OptionSmtpSecureEnum;
  /**
   * 🔐SMTP 账户
   */
  smtp_username?: string;
  /**
   * 🔐阿里云 AccessKey ID
   */
  storage_aliyun_access_id?: string;
  /**
   * 🔐阿里云 Access Key Secret
   */
  storage_aliyun_access_secret?: string;
  /**
   * 🔐阿里云 OSS 的 Bucket 名称
   */
  storage_aliyun_bucket?: string;
  /**
   * 🔐阿里云 OSS 的存储子目录
   */
  storage_aliyun_dir?: string;
  /**
   * 🔐阿里云 OSS 的 EndPoint
   */
  storage_aliyun_endpoint?: string;
  /**
   * 🔐FTP 服务器地址
   */
  storage_ftp_host?: string;
  /**
   * 🔐是否使用被动传输模式。true（被动模式）；false（主动模式）
   */
  storage_ftp_passive?: boolean;
  /**
   * 🔐FTP 密码
   */
  storage_ftp_password?: string;
  /**
   * 🔐FTP 端口号
   */
  storage_ftp_port?: number;
  /**
   * 🔐FTP 存储目录
   */
  storage_ftp_dir?: string;
  /**
   * 🔐FTP 是否启用 SSL。true（启用）；false（不启用）
   */
  storage_ftp_ssl?: boolean;
  /**
   * 🔐FTP 用户名
   */
  storage_ftp_username?: string;
  /**
   * 🔐本地文件存储目录
   */
  storage_local_dir?: string;
  /**
   * 🔐七牛云 AccessKey
   */
  storage_qiniu_access_id?: string;
  /**
   * 🔐七牛云 SecretKey
   */
  storage_qiniu_access_secret?: string;
  /**
   * 🔐七牛云 Bucket
   */
  storage_qiniu_bucket?: string;
  /**
   * 🔐七牛云的存储子目录
   */
  storage_qiniu_dir?: string;
  /**
   * 🔐FTP 存储区域。z0（华东）；z1（华北）；z2（华南）；na0（北美）；as0（东南亚）
   */
  storage_qiniu_zone?: OptionStorageQiniuZoneEnum;
  /**
   * 🔐SFTP 服务器地址
   */
  storage_sftp_host?: string;
  /**
   * 🔐SFTP 密码
   */
  storage_sftp_password?: string;
  /**
   * 🔐SFTP 端口号
   */
  storage_sftp_port?: number;
  /**
   * 🔐SFTP 存储目录
   */
  storage_sftp_dir?: string;
  /**
   * 🔐SFTP 用户名
   */
  storage_sftp_username?: string;
  /**
   * 🔐存储类型
   */
  storage_type?: OptionStorageTypeEnum;
  /**
   * 🔐又拍云 Bucket
   */
  storage_upyun_bucket?: string;
  /**
   * 🔐又拍云的存储子目录
   */
  storage_upyun_dir?: string;
  /**
   * 🔐又拍云操作员账号
   */
  storage_upyun_operator?: string;
  /**
   * 🔐又拍云操作员密码
   */
  storage_upyun_password?: string;
  /**
   * 🔐本地文件访问链接
   */
  storage_url?: string;
  /**
   * 🔐主题名称
   */
  theme?: string;
}

export interface Pagination {
  /**
   * 当前页码
   */
  page: number;
  /**
   * 每页条数
   */
  per_page: number;
  /**
   * 上一页页码，为 `null` 表示没有上一页
   */
  previous: number;
  /**
   * 下一页页码，为 `null` 表示没有下一页
   */
  next: number;
  /**
   * 数据总数
   */
  total: number;
  /**
   * 总页数
   */
  pages: number;
}

export interface Question {
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 提问标题
   */
  title: string;
  /**
   * Markdown 格式的提问内容
   */
  content_markdown: string;
  /**
   * HTML 格式的提问内容
   */
  content_rendered: string;
  /**
   * 评论数量
   */
  comment_count: number;
  /**
   * 回答数量
   */
  answer_count: number;
  /**
   * 关注者数量
   */
  follower_count: number;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  vote_count: number;
  /**
   * 赞成票总数
   */
  vote_up_count: number;
  /**
   * 反对票总数
   */
  vote_down_count: number;
  /**
   * 最后回答时间
   */
  last_answer_time: number;
  /**
   * 创建时间
   */
  create_time: number;
  /**
   * 更新时间（更新提问本身，或在提问下发表回答，都会更新该字段）
   */
  update_time: number;
  /**
   * 🔐删除时间
   */
  delete_time?: number;
  relationships?: QuestionRelationship;
}

export interface QuestionCreateRequestBody {
  /**
   * 标题
   */
  title: string;
  /**
   * 话题ID
   */
  topic_ids: Array<number>;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

export interface QuestionInRelationship {
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 提问标题
   */
  title: string;
  /**
   * 发布时间
   */
  create_time: number;
  /**
   * 更新时间
   */
  update_time: number;
}

export interface QuestionRelationship {
  user?: UserInRelationship;
  topics?: Array<TopicInRelationship>;
  /**
   * 当前登录用户是否已关注该提问
   */
  is_following?: boolean;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: QuestionRelationshipVotingEnum;
}

/**
 * Enum for the voting property.
 */
export type QuestionRelationshipVotingEnum = 'up' | 'down' | '';

export interface QuestionResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Question;
}

export interface QuestionUpdateRequestBody {
  /**
   * 标题
   */
  title?: string;
  /**
   * 话题ID
   */
  topic_ids?: Array<number>;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

export interface QuestionsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Question>;
  pagination: Pagination;
}

/**
 * 举报
 */
export interface Report {
  /**
   * 举报ID
   */
  report_id: number;
  /**
   * 举报目标的ID
   */
  reportable_id: number;
  /**
   * 举报目标类型
   */
  reportable_type: ReportReportableTypeEnum;
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 举报理由
   */
  reason: string;
  /**
   * 举报时间
   */
  create_time: number;
  relationships?: ReportRelationship;
}

/**
 * Enum for the reportable_type property.
 */
export type ReportReportableTypeEnum =
  | 'question'
  | 'answer'
  | 'article'
  | 'comment'
  | 'user';

/**
 * 举报集合
 */
export interface ReportGroup {
  /**
   * 举报目标的ID
   */
  reportable_id: number;
  /**
   * 举报目标类型
   */
  reportable_type: ReportGroupReportableTypeEnum;
  /**
   * 指定对象的被举报数量
   */
  reporter_count: number;
  relationships?: ReportGroupRelationship;
}

/**
 * Enum for the reportable_type property.
 */
export type ReportGroupReportableTypeEnum =
  | 'question'
  | 'answer'
  | 'article'
  | 'comment'
  | 'user';

export interface ReportGroupRelationship {
  question?: QuestionInRelationship;
  answer?: AnswerInRelationship;
  article?: ArticleInRelationship;
  comment?: CommentInRelationship;
  user?: UserInRelationship;
}

export interface ReportGroupsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<ReportGroup>;
  pagination: Pagination;
}

export interface ReportRelationship {
  reporter?: UserInRelationship;
  question?: QuestionInRelationship;
  answer?: AnswerInRelationship;
  article?: ArticleInRelationship;
  comment?: CommentInRelationship;
  user?: UserInRelationship;
}

export interface ReportRequestBody {
  /**
   * 举报理由
   */
  reason: string;
}

export interface ReportResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Report;
}

export interface ReportsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Report>;
  pagination: Pagination;
}

export interface Stats {
  system_info?: SystemInfo;
  /**
   * 用户总数（含被禁用）
   */
  total_user?: number;
  /**
   * 提问总数
   */
  total_question?: number;
  /**
   * 文章总数
   */
  total_article?: number;
  /**
   * 回答总数
   */
  total_answer?: number;
  /**
   * 评论总数
   */
  total_comment?: number;
  /**
   * 每日用户增长数量
   */
  new_user?: Array<StatsNewItems>;
  /**
   * 每日提问增长数量
   */
  new_question?: Array<StatsNewItems>;
  /**
   * 每日文章增长数量
   */
  new_article?: Array<StatsNewItems>;
  /**
   * 每日回答增长数量
   */
  new_answer?: Array<StatsNewItems>;
  /**
   * 每日评论增长数量
   */
  new_comment?: Array<StatsNewItems>;
}

export interface StatsNewItems {
  /**
   * 日期
   */
  date: string;
  /**
   * 当日新增数量
   */
  count: number;
}

export interface StatsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Stats;
}

export interface SystemInfo {
  /**
   * MDClub 的版本号
   */
  mdclub_version?: string;
  /**
   * 操作系统版本
   */
  os_version?: string;
  /**
   * PHP 版本号
   */
  php_version?: string;
  /**
   * Web Server 版本号
   */
  webserver_version?: string;
  /**
   * 数据库版本
   */
  database_version?: string;
  /**
   * 服务器上传文件大小限制
   */
  upload_max_filesize?: string;
  /**
   * PHP 执行时间限制
   */
  max_execution_time?: string;
  /**
   * 服务器剩余硬盘容量
   */
  disk_free_space?: string;
  /**
   * 数据库大小
   */
  database_size?: string;
}

export interface Token {
  /**
   * Token 字符串
   */
  token: string;
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 设备信息
   */
  device: string;
  /**
   * 创建时间
   */
  create_time: number;
  /**
   * 更新时间
   */
  update_time: number;
  /**
   * 过期时间
   */
  expire_time: number;
}

export interface TokenResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Token;
}

export interface Topic {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 话题名称
   */
  name: string;
  cover: TopicCover;
  /**
   * 话题描述
   */
  description: string;
  /**
   * 文章数量
   */
  article_count: number;
  /**
   * 提问数量
   */
  question_count: number;
  /**
   * 关注者数量
   */
  follower_count: number;
  /**
   * 🔐删除时间
   */
  delete_time?: number;
  relationships?: TopicRelationship;
}

export interface TopicCover {
  /**
   * 封面原图地址
   */
  original: string;
  /**
   * 小型封面地址
   */
  small: string;
  /**
   * 中型封面地址
   */
  middle: string;
  /**
   * 大型封面地址
   */
  large: string;
}

export interface TopicCreateRequestBody {
  /**
   * 话题名称
   */
  name: string;
  /**
   * 话题描述
   */
  description: string;
  /**
   * 封面图片
   */
  cover: any;
}

export interface TopicInRelationship {
  /**
   * 话题 ID
   */
  topic_id: number;
  /**
   * 话题名称
   */
  name: string;
  cover: TopicCover;
}

export interface TopicRelationship {
  /**
   * 当前登录用户是否已关注该话题
   */
  is_following?: boolean;
}

export interface TopicResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Topic;
}

export interface TopicUpdateRequestBody {
  /**
   * 话题名称
   */
  name?: string;
  /**
   * 话题描述
   */
  description?: string;
  /**
   * 封面图片
   */
  cover?: any;
}

export interface TopicsResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<Topic>;
  pagination: Pagination;
}

export interface User {
  /**
   * 用户 ID
   */
  user_id: number;
  /**
   * 用户名
   */
  username: string;
  /**
   * 🔐邮箱
   */
  email?: string;
  avatar: UserAvatar;
  cover: UserCover;
  /**
   * 🔐注册 IP
   */
  create_ip?: string;
  /**
   * 🔐注册地
   */
  create_location?: string;
  /**
   * 🔐最后登陆时间
   */
  last_login_time?: number;
  /**
   * 🔐最后登陆 IP
   */
  last_login_ip?: string;
  /**
   * 🔐最后登录地
   */
  last_login_location?: string;
  /**
   * 关注者数量
   */
  follower_count: number;
  /**
   * 关注的用户数量
   */
  followee_count: number;
  /**
   * 关注的文章数量
   */
  following_article_count: number;
  /**
   * 关注的提问数量
   */
  following_question_count: number;
  /**
   * 关注的话题数量
   */
  following_topic_count: number;
  /**
   * 发表的文章数量
   */
  article_count: number;
  /**
   * 发表的提问数量
   */
  question_count: number;
  /**
   * 发表的提问回答数量
   */
  answer_count: number;
  /**
   * 🔐未读通知数量
   */
  notification_unread?: number;
  /**
   * 🔐未读私信数量
   */
  inbox_unread?: number;
  /**
   * 一句话介绍自己
   */
  headline: string;
  /**
   * 个人简介
   */
  bio: string;
  /**
   * 个人主页链接
   */
  blog: string;
  /**
   * 所属企业
   */
  company: string;
  /**
   * 所在地区
   */
  location: string;
  /**
   * 注册时间
   */
  create_time: number;
  /**
   * 🔐更新时间
   */
  update_time?: number;
  /**
   * 🔐禁用时间
   */
  disable_time?: number;
  relationships?: UserRelationship;
}

export interface UserAvatar {
  /**
   * 头像原图地址
   */
  original: string;
  /**
   * 小头像地址
   */
  small: string;
  /**
   * 中头像地址
   */
  middle: string;
  /**
   * 大头像地址
   */
  large: string;
}

export interface UserAvatarRequestBody {
  /**
   * 用户头像
   */
  avatar: any;
}

export interface UserAvatarResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: UserAvatar;
}

export interface UserCover {
  /**
   * 封面原图地址
   */
  original: string;
  /**
   * 小型封面地址
   */
  small: string;
  /**
   * 中型封面地址
   */
  middle: string;
  /**
   * 大型封面地址
   */
  large: string;
}

export interface UserCoverRequestBody {
  /**
   * 用户封面
   */
  cover: any;
}

export interface UserCoverResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: UserCover;
}

export interface UserInRelationship {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 用户名
   */
  username: string;
  /**
   * 一句话介绍
   */
  headline: string;
  avatar: UserAvatar;
}

export interface UserLoginRequestBody {
  /**
   * 用户名或邮箱
   */
  name: string;
  /**
   * 经过 hash1 加密后的密码
   */
  password: string;
  /**
   * 设备信息
   */
  device?: string;
  /**
   * 图形验证码token。若上一次请求返回了 captcha_token， 则必须传该参数
   */
  captcha_token?: string;
  /**
   * 图形验证码的值。若上一次请求返回了 captcha_token，则必须传该参数
   */
  captcha_code?: string;
}

export interface UserPasswordResetRequestBody {
  /**
   * 邮箱
   */
  email: string;
  /**
   * 邮箱验证码
   */
  email_code: string;
  /**
   * hash1 加密后的密码
   */
  password: string;
}

export interface UserRegisterRequestBody {
  /**
   * 邮箱
   */
  email: string;
  /**
   * 邮箱验证码
   */
  email_code: string;
  /**
   * 用户名
   */
  username: string;
  /**
   * hash1 加密后的密码
   */
  password: string;
}

export interface UserRelationship {
  /**
   * 该用户是否是当前登录用户
   */
  is_me?: boolean;
  /**
   * 当前登录用户是否已关注该用户
   */
  is_following?: boolean;
  /**
   * 该用户是否已关注当前登录用户
   */
  is_followed?: boolean;
}

export interface UserRequestBody {
  /**
   * 一句话介绍
   */
  headline?: string;
  /**
   * 个人简介
   */
  bio?: string;
  /**
   * 个人主页
   */
  blog?: string;
  /**
   * 所属企业
   */
  company?: string;
  /**
   * 所属地区
   */
  location?: string;
}

export interface UserResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: User;
}

export interface UserSendEmailRequestBody {
  /**
   * 邮箱
   */
  email: string;
  /**
   * 图形验证码token。若上一次请求返回了 captcha_token， 则必须传该参数
   */
  captcha_token?: string;
  /**
   * 图形验证码的值。若上一次请求返回了 captcha_token，则必须传该参数
   */
  captcha_code?: string;
}

export interface UsersResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: Array<User>;
  pagination: Pagination;
}

/**
 * 投票数量
 */
export interface VoteCount {
  /**
   * 投票目标获得的总投票量（赞成票 - 反对票），结果可以为负数
   */
  vote_count: number;
  /**
   * 赞成票总数
   */
  vote_up_count: number;
  /**
   * 反对票总数
   */
  vote_down_count: number;
}

export interface VoteCountResponse {
  /**
   * 无任务错误时，状态码为 0
   */
  code: number;
  data: VoteCount;
}

export interface VoteRequestBody {
  /**
   * 投票类型
   */
  type: VoteRequestBodyTypeEnum;
}

/**
 * Enum for the type property.
 */
export type VoteRequestBodyTypeEnum = 'up' | 'down';
