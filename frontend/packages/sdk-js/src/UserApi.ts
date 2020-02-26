// @ts-ignore
import sha1 from 'sha-1';
import {
  getRequest,
  postRequest,
  putRequest,
  patchRequest,
  deleteRequest,
} from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import {
  UserAvatarResponse,
  UsersResponse,
  AnswersResponse,
  ArticlesResponse,
  UserCoverResponse,
  CommentsResponse,
  UserResponse,
  QuestionsResponse,
  TopicsResponse,
  FollowerCountResponse,
  EmptyResponse,
} from './models';

interface AddFollowParams {
  /**
   * 用户ID
   */
  user_id: number;
}

interface DeleteAvatarParams {
  /**
   * 用户ID
   */
  user_id: number;
}

interface DeleteCoverParams {
  /**
   * 用户ID
   */
  user_id: number;
}

interface DeleteFollowParams {
  /**
   * 用户ID
   */
  user_id: number;
}

interface DisableParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface DisableMultipleParams {
  /**
   * 多个用 `,` 分隔的用户ID，最多可提供 100 个 ID
   */
  user_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface EnableParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface EnableMultipleParams {
  /**
   * 多个用 `,` 分隔的用户ID，最多可提供 100 个 ID
   */
  user_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetAnswersParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface GetArticlesParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetCommentsParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface GetFolloweesParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetFollowersParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetFollowingArticlesParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetFollowingQuestionsParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetFollowingTopicsParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface GetListParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `create_time`、`disable_time`、`follower_count`。默认为 `-create_time`。其中 `disable_time` 值仅管理员使用有效。
   */
  order?:
    | 'create_time'
    | 'disable_time'
    | 'follower_count'
    | '-create_time'
    | '-disable_time'
    | '-follower_count';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
  /**
   * 用户ID
   */
  user_id?: number;
  /**
   * 用户名
   */
  username?: string;
  /**
   * 邮箱
   */
  email?: string;
  /**
   * 是否仅获取已禁用的用户
   */
  disabled?: boolean;
}

interface GetMineParams {
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetMyAnswersParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface GetMyArticlesParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetMyCommentsParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface GetMyFolloweesParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetMyFollowersParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetMyFollowingArticlesParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetMyFollowingQuestionsParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetMyFollowingTopicsParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface GetMyQuestionsParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetQuestionsParams {
  /**
   * 用户ID
   */
  user_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-create_time`。其中 `delete_time` 值仅管理员使用有效。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time'
    | '-delete_time';
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface RegisterParams {
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
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface SendPasswordResetEmailParams {
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

interface SendRegisterEmailParams {
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

interface UpdateParams {
  /**
   * 用户ID
   */
  user_id: number;
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
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface UpdateMineParams {
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
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface UpdatePasswordParams {
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

interface UploadMyAvatarParams {
  /**
   * 用户头像
   */
  avatar: File;
}

interface UploadMyCoverParams {
  /**
   * 用户封面
   */
  cover: File;
}

/**
 * 添加关注
 * 添加关注
 */
export const addFollow = (
  params: AddFollowParams,
): Promise<FollowerCountResponse> =>
  postRequest(buildURL('/users/{user_id}/followers', params));

/**
 * 🔐删除指定用户的头像，并重置为默认头像
 * 仅管理员可调用该接口
 */
export const deleteAvatar = (
  params: DeleteAvatarParams,
): Promise<UserAvatarResponse> =>
  deleteRequest(buildURL('/users/{user_id}/avatar', params));

/**
 * 🔐删除指定用户的封面，并重置为默认封面
 * 仅管理员可调用该接口
 */
export const deleteCover = (
  params: DeleteCoverParams,
): Promise<UserCoverResponse> =>
  deleteRequest(buildURL('/users/{user_id}/cover', params));

/**
 * 取消关注
 * 取消关注
 */
export const deleteFollow = (
  params: DeleteFollowParams,
): Promise<FollowerCountResponse> =>
  deleteRequest(buildURL('/users/{user_id}/followers', params));

/**
 * 删除当前登录用户的头像，并重置为默认头像
 * 删除当前登录用户的头像，并重置为默认头像
 */
export const deleteMyAvatar = (): Promise<UserAvatarResponse> =>
  deleteRequest(buildURL('/user/avatar', {}));

/**
 * 删除当前登录用户的封面，并重置为默认封面
 * 删除当前登录用户的封面，并重置为默认封面
 */
export const deleteMyCover = (): Promise<UserCoverResponse> =>
  deleteRequest(buildURL('/user/cover', {}));

/**
 * 🔐禁用指定用户
 * 仅管理员可调用该接口
 */
export const disable = (params: DisableParams): Promise<UserResponse> =>
  postRequest(buildURL('/users/{user_id}/disable', params, ['include']));

/**
 * 🔐批量禁用用户
 * 仅管理员可调用该接口。
 */
export const disableMultiple = (
  params: DisableMultipleParams,
): Promise<UsersResponse> =>
  postRequest(buildURL('/users/{user_ids}/disable', params, ['include']));

/**
 * 🔐恢复指定用户
 * 仅管理员可调用该接口。
 */
export const enable = (params: EnableParams): Promise<UserResponse> =>
  postRequest(buildURL('/users/{user_id}/enable', params, ['include']));

/**
 * 🔐批量恢复用户
 * 仅管理员可调用该接口。
 */
export const enableMultiple = (
  params: EnableMultipleParams,
): Promise<UsersResponse> =>
  postRequest(buildURL('/users/{user_ids}/enable', params, ['include']));

/**
 * 获取指定用户信息
 * 若是管理员调用该接口、或当前登录用户读取自己的个人信息，将返回用户的所有信息。 其他情况仅返回部分字段（去掉了隐私信息，隐私字段已用 🔐 标明）
 */
export const get = (params: GetParams): Promise<UserResponse> =>
  getRequest(buildURL('/users/{user_id}', params, ['include']));

/**
 * 获取指定用户发表的回答
 * 获取指定用户发表的回答
 */
export const getAnswers = (
  params: GetAnswersParams,
): Promise<AnswersResponse> =>
  getRequest(
    buildURL('/users/{user_id}/answers', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定用户发表的文章
 * 获取指定用户发表的文章
 */
export const getArticles = (
  params: GetArticlesParams,
): Promise<ArticlesResponse> =>
  getRequest(
    buildURL('/users/{user_id}/articles', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定用户发表的评论
 * 获取指定用户发表的评论
 */
export const getComments = (
  params: GetCommentsParams,
): Promise<CommentsResponse> =>
  getRequest(
    buildURL('/users/{user_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定用户关注的用户列表
 * 获取指定用户关注的用户列表
 */
export const getFollowees = (
  params: GetFolloweesParams,
): Promise<UsersResponse> =>
  getRequest(
    buildURL('/users/{user_id}/followees', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取指定用户的关注者
 * 获取指定用户的关注者
 */
export const getFollowers = (
  params: GetFollowersParams,
): Promise<UsersResponse> =>
  getRequest(
    buildURL('/users/{user_id}/followers', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取指定用户关注的文章列表
 * 获取指定用户关注的文章列表
 */
export const getFollowingArticles = (
  params: GetFollowingArticlesParams,
): Promise<ArticlesResponse> =>
  getRequest(
    buildURL('/users/{user_id}/following_articles', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取指定用户关注的提问列表
 * 获取指定用户关注的提问列表
 */
export const getFollowingQuestions = (
  params: GetFollowingQuestionsParams,
): Promise<QuestionsResponse> =>
  getRequest(
    buildURL('/users/{user_id}/following_questions', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取指定用户关注的话题列表
 * 获取指定用户关注的话题列表
 */
export const getFollowingTopics = (
  params: GetFollowingTopicsParams,
): Promise<TopicsResponse> =>
  getRequest(
    buildURL('/users/{user_id}/following_topics', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取用户列表
 * 仅管理员可使用 email 参数进行搜索  仅管理员可获取已禁用的用户列表
 */
export const getList = (params: GetListParams = {}): Promise<UsersResponse> =>
  getRequest(
    buildURL('/users', params, [
      'page',
      'per_page',
      'order',
      'include',
      'user_id',
      'username',
      'email',
      'disabled',
    ]),
  );

/**
 * 获取当前登录用户的信息
 * 获取当前登录用户的信息
 */
export const getMine = (params: GetMineParams = {}): Promise<UserResponse> =>
  getRequest(buildURL('/user', params, ['include']));

/**
 * 获取当前登录用户发表的回答
 * 获取当前登录用户发表的回答
 */
export const getMyAnswers = (
  params: GetMyAnswersParams = {},
): Promise<AnswersResponse> =>
  getRequest(
    buildURL('/user/answers', params, ['page', 'per_page', 'order', 'include']),
  );

/**
 * 获取当前登录用户发表的文章
 * 获取当前登录用户发表的文章
 */
export const getMyArticles = (
  params: GetMyArticlesParams = {},
): Promise<ArticlesResponse> =>
  getRequest(
    buildURL('/user/articles', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取当前登录用户发表的评论
 * 获取当前登录用户发表的评论
 */
export const getMyComments = (
  params: GetMyCommentsParams = {},
): Promise<CommentsResponse> =>
  getRequest(
    buildURL('/user/comments', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取当前登录用户关注的用户
 * 获取当前登录用户关注的用户
 */
export const getMyFollowees = (
  params: GetMyFolloweesParams = {},
): Promise<UsersResponse> =>
  getRequest(
    buildURL('/user/followees', params, ['page', 'per_page', 'include']),
  );

/**
 * 获取当前登录用户的关注者
 * 获取当前登录用户的关注者
 */
export const getMyFollowers = (
  params: GetMyFollowersParams = {},
): Promise<UsersResponse> =>
  getRequest(
    buildURL('/user/followers', params, ['page', 'per_page', 'include']),
  );

/**
 * 获取登录用户关注的文章
 * 获取登录用户关注的文章
 */
export const getMyFollowingArticles = (
  params: GetMyFollowingArticlesParams = {},
): Promise<ArticlesResponse> =>
  getRequest(
    buildURL('/user/following_articles', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取登录用户关注的提问
 * 获取登录用户关注的提问
 */
export const getMyFollowingQuestions = (
  params: GetMyFollowingQuestionsParams = {},
): Promise<QuestionsResponse> =>
  getRequest(
    buildURL('/user/following_questions', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取登录用户关注的话题
 * 获取登录用户关注的话题
 */
export const getMyFollowingTopics = (
  params: GetMyFollowingTopicsParams = {},
): Promise<TopicsResponse> =>
  getRequest(
    buildURL('/user/following_topics', params, ['page', 'per_page', 'include']),
  );

/**
 * 获取登录用户发表的提问
 * 获取登录用户发表的提问
 */
export const getMyQuestions = (
  params: GetMyQuestionsParams = {},
): Promise<QuestionsResponse> =>
  getRequest(
    buildURL('/user/questions', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定用户发表的提问
 * 获取指定用户发表的提问
 */
export const getQuestions = (
  params: GetQuestionsParams,
): Promise<QuestionsResponse> =>
  getRequest(
    buildURL('/users/{user_id}/questions', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 验证邮箱并创建账号
 * 返回用户信息
 */
export const register = (params: RegisterParams): Promise<UserResponse> => {
  if (params.password) {
    params.password = sha1(params.password);
  }

  return postRequest(
    buildURL('/users', params, ['include']),
    buildRequestBody(params, ['email', 'email_code', 'username', 'password']),
  );
};

/**
 * 发送重置密码邮箱验证码
 * 若返回参数中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
 */
export const sendPasswordResetEmail = (
  params: SendPasswordResetEmailParams,
): Promise<EmptyResponse> =>
  postRequest(
    buildURL('/user/password/email', params),
    buildRequestBody(params, ['email', 'captcha_token', 'captcha_code']),
  );

/**
 * 发送注册邮箱验证码
 * 若返回信息中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
 */
export const sendRegisterEmail = (
  params: SendRegisterEmailParams,
): Promise<EmptyResponse> =>
  postRequest(
    buildURL('/user/register/email', params),
    buildRequestBody(params, ['email', 'captcha_token', 'captcha_code']),
  );

/**
 * 🔐更新指定用户信息
 * 仅管理员可调用该接口
 */
export const update = (params: UpdateParams): Promise<UserResponse> =>
  patchRequest(
    buildURL('/users/{user_id}', params, ['include']),
    buildRequestBody(params, [
      'headline',
      'bio',
      'blog',
      'company',
      'location',
    ]),
  );

/**
 * 更新当前登录用户信息
 * 更新当前登录用户信息
 */
export const updateMine = (params: UpdateMineParams): Promise<UserResponse> =>
  patchRequest(
    buildURL('/user', params, ['include']),
    buildRequestBody(params, [
      'headline',
      'bio',
      'blog',
      'company',
      'location',
    ]),
  );

/**
 * 验证邮箱并更新密码
 * 验证邮箱并更新密码
 */
export const updatePassword = (
  params: UpdatePasswordParams,
): Promise<EmptyResponse> => {
  if (params.password) {
    params.password = sha1(params.password);
  }

  return putRequest(
    buildURL('/user/password', params),
    buildRequestBody(params, ['email', 'email_code', 'password']),
  );
};

/**
 * 上传当前登录用户的头像
 * 上传当前登录用户的头像
 */
export const uploadMyAvatar = (
  params: UploadMyAvatarParams,
): Promise<UserAvatarResponse> => {
  const formData = new FormData();
  formData.append('avatar', params.avatar);

  return postRequest(buildURL('/user/avatar'), formData);
};

/**
 * 上传当前登录用户的封面
 * 上传当前登录用户的封面
 */
export const uploadMyCover = (
  params: UploadMyCoverParams,
): Promise<UserCoverResponse> => {
  const formData = new FormData();
  formData.append('cover', params.cover);

  return postRequest(buildURL('/user/cover'), formData);
};
