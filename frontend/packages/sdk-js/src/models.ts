
export interface Answer {
  /**
   * 回答ID
   */
  answerId: number;
  /**
   * 提问ID
   */
  questionId: number;
  /**
   * 用户ID
   */
  userId: number;
  /**
   * markdown 格式的内容
   */
  contentMarkdown: string;
  /**
   * html 格式的内容
   */
  contentRendered: string;
  /**
   * 评论数量
   */
  commentCount: number;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  voteCount: number;
  /**
   * 创建时间
   */
  createTime: number;
  /**
   * 🔐更新时间
   */
  updateTime?: number;
  /**
   * 🔐删除时间
   */
  deleteTime?: number;
  relationships?: AnswerRelationship;
}


export interface AnswerInRelationship {
  /**
   * 回答ID
   */
  answerId: number;
  /**
   * 内容摘要
   */
  contentSummary: string;
  /**
   * 发布时间
   */
  createTime: number;
  /**
   * 更新时间
   */
  updateTime: number;
}


export interface AnswerRelationship {
  user?: UserInRelationship;
  question?: QuestionInRelationship;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: string;
}


export interface AnswerRequestBody {
  /**
   * Markdown 格式的正文
   */
  contentMarkdown?: string;
  /**
   * HTML 格式的正文
   */
  contentRendered?: string;
}


export interface AnswerResponse {
  code: number;
  data?: Answer;
}


export interface AnswersResponse {
  code: number;
  data?: Array<Answer>;
  pagination?: Pagination;
}


export interface Article {
  /**
   * 文章ID
   */
  articleId: number;
  /**
   * 用户ID
   */
  userId: number;
  /**
   * 文章标题
   */
  title: string;
  /**
   * Markdown 格式的文章内容
   */
  contentMarkdown: string;
  /**
   * HTML 格式的文章内容
   */
  contentRendered: string;
  /**
   * 评论数量
   */
  commentCount: number;
  /**
   * 浏览量
   */
  viewCount: number;
  /**
   * 关注者数量
   */
  followerCount: number;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  voteCount: number;
  /**
   * 创建时间
   */
  createTime: number;
  /**
   * 更新时间（用户可以更新自己的文章）
   */
  updateTime: number;
  /**
   * 🔐删除时间
   */
  deleteTime?: number;
  relationships?: ArticleRelationship;
}


export interface ArticleInRelationship {
  /**
   * 文章ID
   */
  articleId?: number;
  /**
   * 文章标题
   */
  title?: string;
  /**
   * 发布时间
   */
  createTime?: number;
  /**
   * 更新时间
   */
  updateTime?: number;
}


export interface ArticleRelationship {
  user?: UserInRelationship;
  topics?: Array<object>;
  /**
   * 当前登录用户是否已关注该文章
   */
  isFollowing?: boolean;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: string;
}


export interface ArticleRequestBody {
  /**
   * 标题
   */
  title?: string;
  /**
   * 话题ID，多个ID用“,”分隔，最多支持 10 个ID
   */
  topicId?: string;
  /**
   * Markdown 格式的正文
   */
  contentMarkdown?: string;
  /**
   * HTML 格式的正文
   */
  contentRendered?: string;
}


export interface ArticleResponse {
  code: number;
  data?: Article;
}


export interface ArticlesResponse {
  code: number;
  data?: Array<Article>;
  pagination?: Pagination;
}


export interface CaptchaResponse {
  code: number;
  data?: CaptchaResponseData;
}


export interface CaptchaResponseData {
  /**
   * 图形验证码token
   */
  captchaToken?: string;
  /**
   * base64格式的图形验证码图片
   */
  captchaImage?: string;
}


export interface Comment {
  /**
   * 评论ID
   */
  commentId: number;
  /**
   * 评论目标的ID
   */
  commentableId: number;
  /**
   * 评论目标类型
   */
  commentableType: CommentCommentableTypeEnum;
  /**
   * 用户ID
   */
  userId: number;
  /**
   * 评论内容
   */
  content: string;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  voteCount: number;
  /**
   * 发表时间
   */
  createTime: number;
  /**
   * 修改时间
   */
  updateTime: number;
  /**
   * 🔐删除时间
   */
  deleteTime?: number;
  relationships?: CommentRelationship;
}

/**
 * Enum for the commentableType property.
 */
export type CommentCommentableTypeEnum = 'article' | 'question' | 'answer';


export interface CommentInRelationship {
  /**
   * 评论ID
   */
  commentId?: number;
  /**
   * 内容摘要
   */
  contentSummary?: string;
  /**
   * 发布时间
   */
  createTime?: number;
  /**
   * 更新时间
   */
  updateTime?: number;
}


export interface CommentRelationship {
  user?: UserInRelationship;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: string;
}


export interface CommentRequestBody {
  /**
   * 评论内容
   */
  content: string;
}


export interface CommentResponse {
  code: number;
  data?: Comment;
}


export interface CommentsResponse {
  code: number;
  data?: Array<Comment>;
  pagination?: Pagination;
}


export interface Email {
  /**
   * 邮箱地址，多个地址间用“,”分隔，最多支持100个
   */
  email: string;
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
  code: number;
  data?: Email;
}


export interface EmptyResponse {
  code: number;
  data?: object;
}


export interface ErrorField {
  /**
   * 错误字段名
   */
  field: string;
  /**
   * 错误描述
   */
  message: string;
}


export interface ErrorResponse {
  /**
   * 错误代码
   */
  code: number;
  /**
   * 错误描述
   */
  message?: string;
  /**
   * 额外的错误描述
   */
  extraMessage?: string;
  /**
   * 图形验证码token。若返回了该参数，表示下次调用该接口需要输入图形验证码
   */
  captchaToken?: string;
  /**
   * 图形验证码的base64格式图片
   */
  captchaImage?: string;
  errors?: Array<ErrorField>;
}


/**
 * 关注者数量
 */
export interface FollowerCount {
  /**
   * 关注者数量
   */
  followerCount: number;
}


export interface FollowerCountResponse {
  code: number;
  data?: FollowerCount;
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
  createTime: number;
  /**
   * 图片关联对象类型
   */
  itemType: string;
  /**
   * 图片管理对象ID
   */
  itemId: number;
  /**
   * 图片上传者ID
   */
  userId: number;
  urls: ImageUrls;
  relationships?: ImageRelationship;
}


export interface ImageRelationship {
  user?: UserInRelationship;
  question?: QuestionInRelationship;
  article?: ArticleInRelationship;
  answer?: AnswerInRelationship;
}


export interface ImageResponse {
  code: number;
  data?: Image;
}


export interface ImageUpdateRequestBody {
  /**
   * 图片文件名
   */
  filename?: string;
}


export interface ImageUploadRequestBody {
  /**
   * 图片
   */
  image?: any;
}


export interface ImageUrls {
  /**
   * 原图地址
   */
  o?: string;
  /**
   * 宽度固定，高度自适应的图片地址
   */
  r?: string;
  /**
   * 固定宽高的缩略图地址
   */
  t?: string;
}


export interface ImagesResponse {
  code: number;
  data?: Array<Image>;
  pagination?: Pagination;
}


export interface Option {
  /**
   * 回答作者是否可删除回答。  为 `0` 时，不允许删除； 为 `1` 时，在满足 `answer_can_delete_before` 和 `answer_can_delete_only_no_comment` 的条件时可删除。 
   */
  answerCanDelete: OptionAnswerCanDeleteEnum;
  /**
   * 在发表后多少秒内，允许作者删除回答（为 `0` 时表示不做限制）。仅在 `answer_can_delete` 为 `1` 时该参数才有效。 
   */
  answerCanDeleteBefore: string;
  /**
   * 仅在没有评论时，允许作者删除回答。仅在 `answer_can_delete` 为 `1` 时该参数才有效。 
   */
  answerCanDeleteOnlyNoComment: OptionAnswerCanDeleteOnlyNoCommentEnum;
  /**
   * 回答作者是否可编辑回答。  为 `0` 时，不允许编辑； 为 `1` 时，在满足 `answer_can_edit_before` 和 `answer_can_edit_only_no_comment` 的条件时可编辑。 
   */
  answerCanEdit: OptionAnswerCanEditEnum;
  /**
   * 在发表后的多少秒内，允许作者编辑回答（为 `0` 时表示不做限制）。仅在 `answer_can_edit` 为 `1` 时该参数才有效。 
   */
  answerCanEditBefore: string;
  /**
   * 仅在没有评论时，允许作者编辑回答。仅在 `answer_can_edit` 为 `1` 时该参数才有效。 
   */
  answerCanEditOnlyNoComment: OptionAnswerCanEditOnlyNoCommentEnum;
  /**
   * 文章作者是否可删除文章。  为 `0` 时，不允许删除； 为 `1` 时，在满足 `article_can_delete_before` 和 `article_can_delete_only_no_comment` 的条件时可删除。 
   */
  articleCanDelete: OptionArticleCanDeleteEnum;
  /**
   * 在发表后多少秒内，允许作者删除文章（为 `0` 时表示不做限制）。仅在 `article_can_delete` 为 `1` 时该参数才有效。 
   */
  articleCanDeleteBefore: string;
  /**
   * 仅在没有评论时，允许作者删除文章。仅在 `article_can_delete` 为 `1` 时该参数才有效。 
   */
  articleCanDeleteOnlyNoComment: OptionArticleCanDeleteOnlyNoCommentEnum;
  /**
   * 文章作者是否可编辑文章。  为 `0` 时，不允许编辑； 为 `1` 时，在满足 `article_can_edit_before` 和 `article_can_edit_only_no_comment` 的条件时可编辑。 
   */
  articleCanEdit: OptionArticleCanEditEnum;
  /**
   * 在发表后的多少秒内，允许作者编辑文章（为 `0` 时表示不做限制）。仅在 `article_can_edit` 为 `1` 时该参数才有效。 
   */
  articleCanEditBefore: string;
  /**
   * 仅在没有评论时，允许作者编辑文章。仅在 `article_can_edit` 为 `1` 时该参数才有效。 
   */
  articleCanEditOnlyNoComment: OptionArticleCanEditOnlyNoCommentEnum;
  /**
   * 🔐Memcached 服务器地址
   */
  cacheMemcachedHost?: string;
  /**
   * 🔐Memcached 密码
   */
  cacheMemcachedPassword?: string;
  /**
   * 🔐Memcached 端口号
   */
  cacheMemcachedPort?: string;
  /**
   * 🔐Memcached 用户名
   */
  cacheMemcachedUsername?: string;
  /**
   * 🔐缓存键名前缀（只能包含字符 -+.A-Za-z0-9）
   */
  cachePrefix?: string;
  /**
   * 🔐Redis 服务器地址
   */
  cacheRedisHost?: string;
  /**
   * 🔐Redis 密码
   */
  cacheRedisPassword?: string;
  /**
   * 🔐Redis 端口号
   */
  cacheRedisPort?: string;
  /**
   * 🔐Redis 用户名
   */
  cacheRedisUsername?: string;
  /**
   * 🔐缓存类型
   */
  cacheType?: OptionCacheTypeEnum;
  /**
   * 评论作者是否可删除评论。  为 `0` 时，不允许删除； 为 `1` 时，在满足 `comment_can_delete_before` 的条件时可删除。 
   */
  commentCanDelete: OptionCommentCanDeleteEnum;
  /**
   * 在发表后多少秒内，允许作者删除评论（为 `0` 时表示不做限制）。仅在 `comment_can_delete` 为 `1` 时该参数才有效。 
   */
  commentCanDeleteBefore: string;
  /**
   * 评论作者是否可编辑评论。  为 `0` 时，不允许编辑； 为 `1` 时，在满足 `comment_can_edit_before` 的条件时可编辑。 
   */
  commentCanEdit: OptionCommentCanEditEnum;
  /**
   * 在发表后的多少秒内，允许作者编辑评论（为 `0` 时表示不做限制）。仅在 `comment_can_edit` 为 `1` 时该参数才有效。 
   */
  commentCanEditBefore: string;
  /**
   * 系统语言
   */
  language: OptionLanguageEnum;
  /**
   * 提问作者是否可删除提问。  为 `0` 时，不允许删除； 为 `1` 时，在满足 `question_can_delete_before`、`question_can_delete_only_no_answer` 和 `question_can_delete_only_no_comment` 的条件时可删除。 
   */
  questionCanDelete: OptionQuestionCanDeleteEnum;
  /**
   * 在发表后多少秒内，允许作者删除提问（为 `0` 时表示不做限制）。仅在 `question_can_delete` 为 `1` 时该参数才有效。 
   */
  questionCanDeleteBefore: string;
  /**
   * 仅在没有回答时，允许作者删除提问。仅在 `question_can_delete` 为 `1` 时该参数才有效。 
   */
  questionCanDeleteOnlyNoAnswer: OptionQuestionCanDeleteOnlyNoAnswerEnum;
  /**
   * 仅在没有评论时，允许作者删除提问。仅在 `question_can_delete` 为 `1` 时该参数才有效。 
   */
  questionCanDeleteOnlyNoComment: OptionQuestionCanDeleteOnlyNoCommentEnum;
  /**
   * 提问作者是否可编辑提问。  为 `0` 时，不允许编辑； 为 `1` 时，在满足 `question_can_edit_before`、`question_can_edit_only_no_answer` 和 `question_can_edit_only_no_comment` 的条件时可编辑。 
   */
  questionCanEdit: OptionQuestionCanEditEnum;
  /**
   * 在发表后的多少秒内，允许作者编辑提问（为 `0` 时表示不做限制）。仅在 `question_can_edit` 为 `1` 时该参数才有效。 
   */
  questionCanEditBefore: string;
  /**
   * 仅在没有回答时，允许作者编辑提问。仅在 `question_can_edit` 为 `1` 时该参数才有效。 
   */
  questionCanEditOnlyNoAnswer: OptionQuestionCanEditOnlyNoAnswerEnum;
  /**
   * 仅在没有评论时，允许作者编辑提问。仅在 `question_can_edit` 为 `1` 时该参数才有效。 
   */
  questionCanEditOnlyNoComment: OptionQuestionCanEditOnlyNoCommentEnum;
  /**
   * 站点简介
   */
  siteDescription: string;
  /**
   * 站点公安备案号
   */
  siteGonganBeian: string;
  /**
   * 站点 ICP 备案号
   */
  siteIcpBeian: string;
  /**
   * 站点关键词
   */
  siteKeywords: string;
  /**
   * 站点名称
   */
  siteName: string;
  /**
   * 🔐静态资源 URL 地址
   */
  siteStaticUrl?: string;
  /**
   * 🔐SMTP 服务器地址
   */
  smtpHost?: string;
  /**
   * 🔐SMTP 密码
   */
  smtpPassword?: string;
  /**
   * 🔐SMTP 端口
   */
  smtpPort?: number;
  /**
   * 🔐SMTP 回信地址
   */
  smtpReplyTo?: string;
  /**
   * 🔐SMTP 加密方式
   */
  smtpSecure?: string;
  /**
   * 🔐SMTP 账户
   */
  smtpUsername?: string;
  /**
   * 🔐阿里云 AccessKey ID
   */
  storageAliyunAccessId?: string;
  /**
   * 🔐阿里云 Access Key Secret
   */
  storageAliyunAccessSecret?: string;
  /**
   * 🔐阿里云 OSS 的 Bucket 名称
   */
  storageAliyunBucket?: string;
  /**
   * 🔐阿里云 OSS 的 EndPoint
   */
  storageAliyunEndpoint?: string;
  /**
   * 🔐FTP 服务器地址
   */
  storageFtpHost?: string;
  /**
   * 🔐是否使用被动传输模式。1（被动模式）；0（主动模式）
   */
  storageFtpPassive?: OptionStorageFtpPassiveEnum;
  /**
   * 🔐FTP 密码
   */
  storageFtpPassword?: string;
  /**
   * 🔐FTP 端口号
   */
  storageFtpPort?: number;
  /**
   * 🔐FTP 存储目录
   */
  storageFtpRoot?: string;
  /**
   * 🔐FTP 是否启用 SSL。1（启用）；0（不启用）
   */
  storageFtpSsl?: OptionStorageFtpSslEnum;
  /**
   * 🔐FTP 用户名
   */
  storageFtpUsername?: string;
  /**
   * 🔐本地文件存储目录
   */
  storageLocalDir?: string;
  /**
   * 🔐七牛云 AccessKey
   */
  storageQiniuAccessId?: string;
  /**
   * 🔐七牛云 SecretKey
   */
  storageQiniuAccessSecret?: string;
  /**
   * 🔐七牛云 Bucket
   */
  storageQiniuBucket?: string;
  /**
   * 🔐FTP 存储区域。z0（华东）；z1（华北）；z2（华南）；na0（北美）；as0（东南亚）
   */
  storageQiniuZone?: OptionStorageQiniuZoneEnum;
  /**
   * 🔐SFTP 服务器地址
   */
  storageSftpHost?: string;
  /**
   * 🔐SFTP 密码
   */
  storageSftpPassword?: string;
  /**
   * 🔐SFTP 端口号
   */
  storageSftpPort?: number;
  /**
   * 🔐SFTP 存储目录
   */
  storageSftpRoot?: string;
  /**
   * 🔐SFTP 用户名
   */
  storageSftpUsername?: string;
  /**
   * 🔐存储类型
   */
  storageType?: OptionStorageTypeEnum;
  /**
   * 🔐又拍云 Bucket
   */
  storageUpyunBucket?: string;
  /**
   * 🔐又拍云操作员账号
   */
  storageUpyunOperator?: string;
  /**
   * 🔐又拍云操作员密码
   */
  storageUpyunPassword?: string;
  /**
   * 🔐本地文件访问链接
   */
  storageUrl?: string;
  /**
   * 🔐主题名称
   */
  theme?: string;
}

/**
 * Enum for the answerCanDelete property.
 */
export type OptionAnswerCanDeleteEnum = '0' | '1';

/**
 * Enum for the answerCanDeleteOnlyNoComment property.
 */
export type OptionAnswerCanDeleteOnlyNoCommentEnum = '0' | '1';

/**
 * Enum for the answerCanEdit property.
 */
export type OptionAnswerCanEditEnum = '0' | '1';

/**
 * Enum for the answerCanEditOnlyNoComment property.
 */
export type OptionAnswerCanEditOnlyNoCommentEnum = '0' | '1';

/**
 * Enum for the articleCanDelete property.
 */
export type OptionArticleCanDeleteEnum = '0' | '1';

/**
 * Enum for the articleCanDeleteOnlyNoComment property.
 */
export type OptionArticleCanDeleteOnlyNoCommentEnum = '0' | '1';

/**
 * Enum for the articleCanEdit property.
 */
export type OptionArticleCanEditEnum = '0' | '1';

/**
 * Enum for the articleCanEditOnlyNoComment property.
 */
export type OptionArticleCanEditOnlyNoCommentEnum = '0' | '1';

/**
 * Enum for the cacheType property.
 */
export type OptionCacheTypeEnum = 'redis' | 'memcached';

/**
 * Enum for the commentCanDelete property.
 */
export type OptionCommentCanDeleteEnum = '0' | '1';

/**
 * Enum for the commentCanEdit property.
 */
export type OptionCommentCanEditEnum = '0' | '1';

/**
 * Enum for the language property.
 */
export type OptionLanguageEnum = 'en' | 'pl' | 'ru' | 'zh-CN' | 'zh-TW';

/**
 * Enum for the questionCanDelete property.
 */
export type OptionQuestionCanDeleteEnum = '0' | '1';

/**
 * Enum for the questionCanDeleteOnlyNoAnswer property.
 */
export type OptionQuestionCanDeleteOnlyNoAnswerEnum = '0' | '1';

/**
 * Enum for the questionCanDeleteOnlyNoComment property.
 */
export type OptionQuestionCanDeleteOnlyNoCommentEnum = '0' | '1';

/**
 * Enum for the questionCanEdit property.
 */
export type OptionQuestionCanEditEnum = '0' | '1';

/**
 * Enum for the questionCanEditOnlyNoAnswer property.
 */
export type OptionQuestionCanEditOnlyNoAnswerEnum = '0' | '1';

/**
 * Enum for the questionCanEditOnlyNoComment property.
 */
export type OptionQuestionCanEditOnlyNoCommentEnum = '0' | '1';

/**
 * Enum for the storageFtpPassive property.
 */
export type OptionStorageFtpPassiveEnum = '1' | '0';

/**
 * Enum for the storageFtpSsl property.
 */
export type OptionStorageFtpSslEnum = '1' | '0';

/**
 * Enum for the storageQiniuZone property.
 */
export type OptionStorageQiniuZoneEnum = 'z0' | 'z1' | 'z2' | 'na0' | 'as0';

/**
 * Enum for the storageType property.
 */
export type OptionStorageTypeEnum = 'local' | 'ftp' | 'sftp' | 'aliyun' | 'upyun' | 'qiniu';


export interface OptionResponse {
  code: number;
  data?: Option;
}


export interface Pagination {
  /**
   * 当前页码
   */
  page: number;
  /**
   * 每页条数
   */
  perPage: number;
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
  questionId: number;
  /**
   * 用户ID
   */
  userId: number;
  /**
   * 提问标题
   */
  title: string;
  /**
   * Markdown 格式的提问内容
   */
  contentMarkdown: string;
  /**
   * HTML 格式的提问内容
   */
  contentRendered: string;
  /**
   * 评论数量
   */
  commentCount: number;
  /**
   * 回答数量
   */
  answerCount: number;
  /**
   * 浏览量
   */
  viewCount: number;
  /**
   * 关注者数量
   */
  followerCount: number;
  /**
   * 投票数（赞成票 - 反对票，可能为负数）
   */
  voteCount: number;
  /**
   * 最后回答时间
   */
  lastAnswerTime: number;
  /**
   * 创建时间
   */
  createTime: number;
  /**
   * 更新时间（更新提问本身，或在提问下发表回答，都会更新该字段）
   */
  updateTime: number;
  /**
   * 🔐删除时间
   */
  deleteTime?: number;
  relationships?: QuestionRelationship;
}


export interface QuestionInRelationship {
  /**
   * 提问ID
   */
  questionId?: number;
  /**
   * 提问标题
   */
  title?: string;
  /**
   * 发布时间
   */
  createTime?: number;
  /**
   * 更新时间
   */
  updateTime?: number;
}


export interface QuestionRelationship {
  user?: UserInRelationship;
  topics?: Array<object>;
  /**
   * 当前登录用户是否已关注该提问
   */
  isFollowing?: boolean;
  /**
   * 当前登录用户的投票类型（up、down），未投过票则为空字符串
   */
  voting?: string;
}


export interface QuestionRequestBody {
  /**
   * 标题
   */
  title?: string;
  /**
   * 话题ID，多个ID用“,”分隔，最多支持 10 个ID
   */
  topicId?: string;
  /**
   * Markdown 格式的正文
   */
  contentMarkdown?: string;
  /**
   * HTML 格式的正文
   */
  contentRendered?: string;
}


export interface QuestionResponse {
  code: number;
  data?: Question;
}


export interface QuestionsResponse {
  code: number;
  data?: Array<Question>;
  pagination?: Pagination;
}


/**
 * 举报
 */
export interface Report {
  /**
   * 举报ID
   */
  reportId: number;
  /**
   * 举报目标的ID
   */
  reportableId: number;
  /**
   * 举报目标类型
   */
  reportableType: ReportReportableTypeEnum;
  /**
   * 用户ID
   */
  userId: number;
  /**
   * 举报理由
   */
  reason: string;
  /**
   * 举报时间
   */
  createTime: string;
  relationships?: ReportRelationship;
}

/**
 * Enum for the reportableType property.
 */
export type ReportReportableTypeEnum = 'question' | 'answer' | 'article' | 'comment' | 'user';


/**
 * 举报集合
 */
export interface ReportGroup {
  /**
   * 举报目标的ID
   */
  reportableId: number;
  /**
   * 举报目标类型
   */
  reportableType: ReportGroupReportableTypeEnum;
  /**
   * 指定对象的被举报数量
   */
  reporterCount: number;
  relationships?: ReportGroupRelationship;
}

/**
 * Enum for the reportableType property.
 */
export type ReportGroupReportableTypeEnum = 'question' | 'answer' | 'article' | 'comment' | 'user';


export interface ReportGroupRelationship {
  question?: QuestionInRelationship;
  answer?: AnswerInRelationship;
  article?: ArticleInRelationship;
  comment?: CommentInRelationship;
  user?: UserInRelationship;
}


export interface ReportGroupsResponse {
  code: number;
  data?: Array<ReportGroup>;
  pagination?: Pagination;
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
  code: number;
  data?: Report;
}


export interface ReportsResponse {
  code: number;
  data?: Array<Report>;
  pagination?: Pagination;
}


export interface Token {
  /**
   * Token 字符串
   */
  token: string;
  /**
   * 用户ID
   */
  userId: number;
  /**
   * 设备信息
   */
  device: string;
  /**
   * 创建时间
   */
  createTime: number;
  /**
   * 更新时间
   */
  updateTime: number;
  /**
   * 过期时间
   */
  expireTime: number;
}


export interface TokenResponse {
  code: number;
  data?: Token;
}


export interface Topic {
  /**
   * 话题ID
   */
  topicId: number;
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
  articleCount: number;
  /**
   * 提问数量
   */
  questionCount: number;
  /**
   * 关注者数量
   */
  followerCount: number;
  /**
   * 🔐删除时间
   */
  deleteTime?: number;
  relationships?: TopicRelationship;
}


export interface TopicCover {
  /**
   * 封面原图地址
   */
  o: string;
  /**
   * 小型封面地址
   */
  s: string;
  /**
   * 中型封面地址
   */
  m: string;
  /**
   * 大型封面地址
   */
  l: string;
}


export interface TopicRelationship {
  /**
   * 当前登录用户是否已关注该话题
   */
  isFollowing?: boolean;
}


export interface TopicRequestBody {
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


export interface TopicResponse {
  code: number;
  data?: Topic;
}


export interface TopicsResponse {
  code: number;
  data?: Array<Topic>;
  pagination?: Pagination;
}


export interface User {
  /**
   * 用户 ID
   */
  userId: number;
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
  createIp?: string;
  /**
   * 🔐注册地
   */
  createLocation?: string;
  /**
   * 🔐最后登陆时间
   */
  lastLoginTime?: number;
  /**
   * 🔐最后登陆 IP
   */
  lastLoginIp?: string;
  /**
   * 🔐最后登录地
   */
  lastLoginLocation?: string;
  /**
   * 关注者数量
   */
  followerCount: number;
  /**
   * 关注的用户数量
   */
  followeeCount: number;
  /**
   * 关注的文章数量
   */
  followingArticleCount: number;
  /**
   * 关注的提问数量
   */
  followingQuestionCount: number;
  /**
   * 关注的话题数量
   */
  followingTopicCount: number;
  /**
   * 发表的文章数量
   */
  articleCount: number;
  /**
   * 发表的提问数量
   */
  questionCount: number;
  /**
   * 发表的提问回答数量
   */
  answerCount: number;
  /**
   * 🔐未读消息数量
   */
  notificationUnread?: number;
  /**
   * 🔐未读私信数量
   */
  inboxUnread?: number;
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
  createTime: number;
  /**
   * 🔐更新时间
   */
  updateTime?: number;
  /**
   * 🔐禁用时间
   */
  disableTime?: number;
  relationships?: UserRelationship;
}


export interface UserAvatar {
  /**
   * 头像原图地址
   */
  o: string;
  /**
   * 小头像地址
   */
  s: string;
  /**
   * 中头像地址
   */
  m: string;
  /**
   * 大头像地址
   */
  l: string;
}


export interface UserAvatarRequestBody {
  /**
   * 用户头像
   */
  avatar?: any;
}


export interface UserAvatarResponse {
  code: number;
  data?: UserAvatar;
}


export interface UserCover {
  /**
   * 封面原图地址
   */
  o: string;
  /**
   * 小型封面地址
   */
  s: string;
  /**
   * 中型封面地址
   */
  m: string;
  /**
   * 大型封面地址
   */
  l: string;
}


export interface UserCoverRequestBody {
  /**
   * 用户封面
   */
  cover?: any;
}


export interface UserCoverResponse {
  code: number;
  data?: UserCover;
}


export interface UserInRelationship {
  /**
   * 用户ID
   */
  userId?: number;
  /**
   * 用户名
   */
  username?: string;
  /**
   * 一句话介绍
   */
  headline?: string;
  avatar?: UserAvatar;
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
  captchaToken?: string;
  /**
   * 图形验证码的值。若上一次请求返回了 captcha_token，则必须传该参数
   */
  captchaCode?: string;
}


export interface UserPasswordResetRequestBody {
  /**
   * 邮箱
   */
  email: string;
  /**
   * 邮箱验证码
   */
  emailCode: string;
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
  emailCode: string;
  /**
   * 用户名
   */
  username: string;
  /**
   * hash1 加密后的密码
   */
  password: string;
  /**
   * 设备信息
   */
  device?: string;
}


export interface UserRelationship {
  /**
   * 该用户是否是当前登录用户
   */
  isMe?: boolean;
  /**
   * 当前登录用户是否已关注该用户
   */
  isFollowing?: boolean;
  /**
   * 该用户是否已关注当前登录用户
   */
  isFollowed?: boolean;
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
  code: number;
  data?: User;
}


export interface UserSendEmailRequestBody {
  /**
   * 邮箱
   */
  email: string;
  /**
   * 图形验证码token。若上一次请求返回了 captcha_token， 则必须传该参数
   */
  captchaToken?: string;
  /**
   * 图形验证码的值。若上一次请求返回了 captcha_token，则必须传该参数
   */
  captchaCode?: string;
}


export interface UsersResponse {
  code: number;
  data?: Array<User>;
  pagination?: Pagination;
}


/**
 * 投票数量
 */
export interface VoteCount {
  /**
   * 投票目标获得的总投票量（赞成票 - 反对票），结果可以为负数
   */
  voteCount: number;
}


export interface VoteCountResponse {
  code: number;
  data?: VoteCount;
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

