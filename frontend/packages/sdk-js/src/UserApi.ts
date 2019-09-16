import { get, post, put, patch, del } from './util/requestAlias';
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
  user_id: number;
}

interface DeleteAvatarParams {
  user_id: number;
}

interface DeleteCoverParams {
  user_id: number;
}

interface DeleteFollowParams {
  user_id: number;
}

interface DisableParams {
  user_id: number;
}

interface DisableMultipleParams {
  user_id?: Array<number>;
}

interface EnableParams {
  user_id: number;
}

interface EnableMultipleParams {
  user_id?: Array<number>;
}

interface GetParams {
  user_id: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetAnswersParams {
  user_id: number;
  page?: number;
  per_page?: number;
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  include?: Array<'user' | 'question' | 'voting'>;
}

interface GetArticlesParams {
  user_id: number;
  page?: number;
  per_page?: number;
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetCommentsParams {
  user_id: number;
  page?: number;
  per_page?: number;
  order?: 'vote_count' | 'create_time' | '-vote_count' | '-create_time';
  include?: Array<'user' | 'voting'>;
}

interface GetDisabledParams {
  page?: number;
  per_page?: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
  order?:
    | 'create_time'
    | 'delete_time'
    | 'follower_count'
    | '-create_time'
    | '-delete_time'
    | '-follower_count';
  user_id?: number;
  username?: string;
  email?: string;
}

interface GetFolloweesParams {
  user_id: number;
  page?: number;
  per_page?: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetFollowersParams {
  user_id: number;
  page?: number;
  per_page?: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetFollowingArticlesParams {
  user_id: number;
  page?: number;
  per_page?: number;
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetFollowingQuestionsParams {
  user_id: number;
  page?: number;
  per_page?: number;
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetFollowingTopicsParams {
  user_id: number;
  page?: number;
  per_page?: number;
  include?: Array<'is_following'>;
}

interface GetListParams {
  page?: number;
  per_page?: number;
  order?: 'create_time' | 'follower_count' | '-create_time' | '-follower_count';
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
  user_id?: number;
  username?: string;
  email?: string;
}

interface GetMineParams {
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetMyAnswersParams {
  page?: number;
  per_page?: number;
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  include?: Array<'user' | 'question' | 'voting'>;
}

interface GetMyArticlesParams {
  page?: number;
  per_page?: number;
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetMyCommentsParams {
  page?: number;
  per_page?: number;
  order?: 'vote_count' | 'create_time' | '-vote_count' | '-create_time';
  include?: Array<'user' | 'voting'>;
}

interface GetMyFolloweesParams {
  page?: number;
  per_page?: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetMyFollowersParams {
  page?: number;
  per_page?: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
}

interface GetMyFollowingArticlesParams {
  page?: number;
  per_page?: number;
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetMyFollowingQuestionsParams {
  page?: number;
  per_page?: number;
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetMyFollowingTopicsParams {
  page?: number;
  per_page?: number;
  include?: Array<'is_following'>;
}

interface GetMyQuestionsParams {
  page?: number;
  per_page?: number;
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetQuestionsParams {
  user_id: number;
  page?: number;
  per_page?: number;
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
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
   * 设备信息
   */
  device?: string;
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
  user_id: number;
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;

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

interface UpdateMineParams {
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
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
  avatar: any;
}

interface UploadMyCoverParams {
  /**
   * 用户封面
   */
  cover: any;
}

const className = 'UserApi';

/**
 * UserApi
 */
export default {
  /**
   * 添加关注
   * 添加关注
   * @param params.user_id 用户ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    return post(
      buildURL(`${className}.addFollow`, '/users/{user_id}/followers', params),
    );
  },

  /**
   * 🔐删除指定用户的头像，并重置为默认头像
   * 仅管理员可调用该接口
   * @param params.user_id 用户ID
   */
  deleteAvatar: (params: DeleteAvatarParams): Promise<UserAvatarResponse> => {
    return del(
      buildURL(`${className}.deleteAvatar`, '/users/{user_id}/avatar', params),
    );
  },

  /**
   * 🔐删除指定用户的封面，并重置为默认封面
   * 仅管理员可调用该接口
   * @param params.user_id 用户ID
   */
  deleteCover: (params: DeleteCoverParams): Promise<UserCoverResponse> => {
    return del(
      buildURL(`${className}.deleteCover`, '/users/{user_id}/cover', params),
    );
  },

  /**
   * 取消关注
   * 取消关注
   * @param params.user_id 用户ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    return del(
      buildURL(
        `${className}.deleteFollow`,
        '/users/{user_id}/followers',
        params,
      ),
    );
  },

  /**
   * 删除当前登录用户的头像，并重置为默认头像
   * 删除当前登录用户的头像，并重置为默认头像
   */
  deleteMyAvatar: (): Promise<UserAvatarResponse> => {
    return del(buildURL(`${className}.deleteMyAvatar`, '/user/avatar', {}));
  },

  /**
   * 删除当前登录用户的封面，并重置为默认封面
   * 删除当前登录用户的封面，并重置为默认封面
   */
  deleteMyCover: (): Promise<UserCoverResponse> => {
    return del(buildURL(`${className}.deleteMyCover`, '/user/cover', {}));
  },

  /**
   * 🔐禁用指定用户
   * 仅管理员可调用该接口
   * @param params.user_id 用户ID
   */
  disable: (params: DisableParams): Promise<EmptyResponse> => {
    return del(buildURL(`${className}.disable`, '/users/{user_id}', params));
  },

  /**
   * 🔐批量禁用用户
   * 仅管理员可调用该接口。只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.user_id 用“,”分隔的用户ID，最多可提供100个ID
   */
  disableMultiple: (params: DisableMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.disableMultiple`, '/users', params, ['user_id']),
    );
  },

  /**
   * 🔐恢复指定用户
   * 仅管理员可调用该接口。
   * @param params.user_id 用户ID
   */
  enable: (params: EnableParams): Promise<UserResponse> => {
    return post(
      buildURL(`${className}.enable`, '/trash/users/{user_id}', params),
    );
  },

  /**
   * 🔐批量恢复用户
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被启用，该接口都会返回成功。
   * @param params.user_id 用“,”分隔的用户ID，最多可提供100个ID
   */
  enableMultiple: (params: EnableMultipleParams): Promise<EmptyResponse> => {
    return post(
      buildURL(`${className}.enableMultiple`, '/trash/users', params, [
        'user_id',
      ]),
    );
  },

  /**
   * 获取指定用户信息
   * 若是管理员调用该接口、或当前登录用户读取自己的个人信息，将返回用户的所有信息。 其他情况仅返回部分字段（去掉了隐私信息，隐私字段已用 🔐 标明）
   * @param params.user_id 用户ID
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  get: (params: GetParams): Promise<UserResponse> => {
    return get(
      buildURL(`${className}.get`, '/users/{user_id}', params, ['include']),
    );
  },

  /**
   * 获取指定用户发表的回答
   * 获取指定用户发表的回答
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;。
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   */
  getAnswers: (params: GetAnswersParams): Promise<AnswersResponse> => {
    return get(
      buildURL(`${className}.getAnswers`, '/users/{user_id}/answers', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]),
    );
  },

  /**
   * 获取指定用户发表的文章
   * 获取指定用户发表的文章
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getArticles: (params: GetArticlesParams): Promise<ArticlesResponse> => {
    return get(
      buildURL(
        `${className}.getArticles`,
        '/users/{user_id}/articles',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 获取指定用户发表的评论
   * 获取指定用户发表的评论
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> => {
    return get(
      buildURL(
        `${className}.getComments`,
        '/users/{user_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 🔐获取已禁用用户列表
   * 仅管理员可调用该接口。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;create_time&#x60;、&#x60;delete_time&#x60;、&#x60;follower_count&#x60;。默认为 &#x60;-delete_time&#x60;
   * @param params.user_id 用户ID
   * @param params.username 用户名
   * @param params.email 邮箱
   */
  getDisabled: (params: GetDisabledParams): Promise<UsersResponse> => {
    return get(
      buildURL(`${className}.getDisabled`, '/trash/users', params, [
        'page',
        'per_page',
        'include',
        'order',
        'user_id',
        'username',
        'email',
      ]),
    );
  },

  /**
   * 获取指定用户关注的用户列表
   * 获取指定用户关注的用户列表
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getFollowees: (params: GetFolloweesParams): Promise<UsersResponse> => {
    return get(
      buildURL(
        `${className}.getFollowees`,
        '/users/{user_id}/followees',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取指定用户的关注者
   * 获取指定用户的关注者
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    return get(
      buildURL(
        `${className}.getFollowers`,
        '/users/{user_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取指定用户关注的文章列表
   * 获取指定用户关注的文章列表
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getFollowingArticles: (
    params: GetFollowingArticlesParams,
  ): Promise<ArticlesResponse> => {
    return get(
      buildURL(
        `${className}.getFollowingArticles`,
        '/users/{user_id}/following_articles',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取指定用户关注的提问列表
   * 获取指定用户关注的提问列表
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getFollowingQuestions: (
    params: GetFollowingQuestionsParams,
  ): Promise<QuestionsResponse> => {
    return get(
      buildURL(
        `${className}.getFollowingQuestions`,
        '/users/{user_id}/following_questions',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取指定用户关注的话题列表
   * 获取指定用户关注的话题列表
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   */
  getFollowingTopics: (
    params: GetFollowingTopicsParams,
  ): Promise<TopicsResponse> => {
    return get(
      buildURL(
        `${className}.getFollowingTopics`,
        '/users/{user_id}/following_topics',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取用户列表
   * 不包含已禁用的用户。仅管理员可使用 email 参数进行搜索。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;create_time&#x60;、&#x60;follower_count&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   * @param params.user_id 用户ID
   * @param params.username 用户名
   * @param params.email 邮箱
   */
  getList: (params: GetListParams): Promise<UsersResponse> => {
    return get(
      buildURL(`${className}.getList`, '/users', params, [
        'page',
        'per_page',
        'order',
        'include',
        'user_id',
        'username',
        'email',
      ]),
    );
  },

  /**
   * 获取当前登录用户的信息
   * 获取当前登录用户的信息
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getMine: (params: GetMineParams): Promise<UserResponse> => {
    return get(buildURL(`${className}.getMine`, '/user', params, ['include']));
  },

  /**
   * 获取当前登录用户发表的回答
   * 获取当前登录用户发表的回答
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;。
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   */
  getMyAnswers: (params: GetMyAnswersParams): Promise<AnswersResponse> => {
    return get(
      buildURL(`${className}.getMyAnswers`, '/user/answers', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]),
    );
  },

  /**
   * 获取当前登录用户发表的文章
   * 获取当前登录用户发表的文章
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getMyArticles: (params: GetMyArticlesParams): Promise<ArticlesResponse> => {
    return get(
      buildURL(`${className}.getMyArticles`, '/user/articles', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]),
    );
  },

  /**
   * 获取当前登录用户发表的评论
   * 获取当前登录用户发表的评论
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  getMyComments: (params: GetMyCommentsParams): Promise<CommentsResponse> => {
    return get(
      buildURL(`${className}.getMyComments`, '/user/comments', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]),
    );
  },

  /**
   * 获取当前登录用户关注的用户
   * 获取当前登录用户关注的用户
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getMyFollowees: (params: GetMyFolloweesParams): Promise<UsersResponse> => {
    return get(
      buildURL(`${className}.getMyFollowees`, '/user/followees', params, [
        'page',
        'per_page',
        'include',
      ]),
    );
  },

  /**
   * 获取当前登录用户的关注者
   * 获取当前登录用户的关注者
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getMyFollowers: (params: GetMyFollowersParams): Promise<UsersResponse> => {
    return get(
      buildURL(`${className}.getMyFollowers`, '/user/followers', params, [
        'page',
        'per_page',
        'include',
      ]),
    );
  },

  /**
   * 获取登录用户关注的文章
   * 获取登录用户关注的文章
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getMyFollowingArticles: (
    params: GetMyFollowingArticlesParams,
  ): Promise<ArticlesResponse> => {
    return get(
      buildURL(
        `${className}.getMyFollowingArticles`,
        '/user/following_articles',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取登录用户关注的提问
   * 获取登录用户关注的提问
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getMyFollowingQuestions: (
    params: GetMyFollowingQuestionsParams,
  ): Promise<QuestionsResponse> => {
    return get(
      buildURL(
        `${className}.getMyFollowingQuestions`,
        '/user/following_questions',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取登录用户关注的话题
   * 获取登录用户关注的话题
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   */
  getMyFollowingTopics: (
    params: GetMyFollowingTopicsParams,
  ): Promise<TopicsResponse> => {
    return get(
      buildURL(
        `${className}.getMyFollowingTopics`,
        '/user/following_topics',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取登录用户发表的提问
   * 获取登录用户发表的提问
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getMyQuestions: (
    params: GetMyQuestionsParams,
  ): Promise<QuestionsResponse> => {
    return get(
      buildURL(`${className}.getMyQuestions`, '/user/questions', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]),
    );
  },

  /**
   * 获取指定用户发表的提问
   * 获取指定用户发表的提问
   * @param params.user_id 用户ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getQuestions: (params: GetQuestionsParams): Promise<QuestionsResponse> => {
    return get(
      buildURL(
        `${className}.getQuestions`,
        '/users/{user_id}/questions',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 验证邮箱并创建账号
   * 返回用户信息
   * @param params.UserRegisterRequestBody
   */
  register: (params: RegisterParams): Promise<UserResponse> => {
    return post(
      buildURL(`${className}.register`, '/users', params),
      buildRequestBody(params, [
        'email',
        'email_code',
        'username',
        'password',
        'device',
      ]),
    );
  },

  /**
   * 发送重置密码邮箱验证码
   * 若返回参数中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   * @param params.UserSendEmailRequestBody
   */
  sendPasswordResetEmail: (
    params: SendPasswordResetEmailParams,
  ): Promise<EmptyResponse> => {
    return post(
      buildURL(
        `${className}.sendPasswordResetEmail`,
        '/user/password/email',
        params,
      ),
      buildRequestBody(params, ['email', 'captcha_token', 'captcha_code']),
    );
  },

  /**
   * 发送注册邮箱验证码
   * 若返回信息中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   * @param params.UserSendEmailRequestBody
   */
  sendRegisterEmail: (
    params: SendRegisterEmailParams,
  ): Promise<EmptyResponse> => {
    return post(
      buildURL(
        `${className}.sendRegisterEmail`,
        '/user/register/email',
        params,
      ),
      buildRequestBody(params, ['email', 'captcha_token', 'captcha_code']),
    );
  },

  /**
   * 🔐更新指定用户信息
   * 仅管理员可调用该接口
   * @param params.user_id 用户ID
   * @param params.UserRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  update: (params: UpdateParams): Promise<UserResponse> => {
    return patch(
      buildURL(`${className}.update`, '/users/{user_id}', params, ['include']),
      buildRequestBody(params, [
        'headline',
        'bio',
        'blog',
        'company',
        'location',
      ]),
    );
  },

  /**
   * 更新当前登录用户信息
   * 更新当前登录用户信息
   * @param params.UserRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  updateMine: (params: UpdateMineParams): Promise<UserResponse> => {
    return patch(
      buildURL(`${className}.updateMine`, '/user', params, ['include']),
      buildRequestBody(params, [
        'headline',
        'bio',
        'blog',
        'company',
        'location',
      ]),
    );
  },

  /**
   * 验证邮箱并更新密码
   * 验证邮箱并更新密码
   * @param params.UserPasswordResetRequestBody
   */
  updatePassword: (params: UpdatePasswordParams): Promise<EmptyResponse> => {
    return put(
      buildURL(`${className}.updatePassword`, '/user/password', params),
      buildRequestBody(params, ['email', 'email_code', 'password']),
    );
  },

  /**
   * 上传当前登录用户的头像
   * 上传当前登录用户的头像
   * @param params.UserAvatarRequestBody
   */
  uploadMyAvatar: (
    params: UploadMyAvatarParams,
  ): Promise<UserAvatarResponse> => {
    return post(
      buildURL(`${className}.uploadMyAvatar`, '/user/avatar', params),
      buildRequestBody(params, ['avatar']),
    );
  },

  /**
   * 上传当前登录用户的封面
   * 上传当前登录用户的封面
   * @param params.UserCoverRequestBody
   */
  uploadMyCover: (params: UploadMyCoverParams): Promise<UserCoverResponse> => {
    return post(
      buildURL(`${className}.uploadMyCover`, '/user/cover', params),
      buildRequestBody(params, ['cover']),
    );
  },
};
