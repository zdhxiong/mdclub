import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  UserAvatarResponse,
  UserSendEmailRequestBody,
  UsersResponse,
  AnswersResponse,
  ArticlesResponse,
  UserRegisterRequestBody,
  UserCoverResponse,
  CommentsResponse,
  UserResponse,
  QuestionsResponse,
  UserRequestBody,
  UserPasswordResetRequestBody,
  TopicsResponse,
  UserAvatarRequestBody,
  UserCoverRequestBody,
  FollowerCountResponse,
  EmptyResponse,
} from './models';

interface AddFollowParams {
  userId: number;
}

interface DeleteAvatarParams {
  userId: number;
}

interface DeleteCoverParams {
  userId: number;
}

interface DeleteFollowParams {
  userId: number;
}

interface DeleteMyAvatarParams {}

interface DeleteMyCoverParams {}

interface DisableParams {
  userId: number;
}

interface DisableMultipleParams {
  userId?: Array<number>;
}

interface EnableParams {
  userId: number;
}

interface EnableMultipleParams {
  userId?: Array<number>;
}

interface GetParams {
  userId: number;
  include?: Array<string>;
}

interface GetAnswersParams {
  userId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetArticlesParams {
  userId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetCommentsParams {
  userId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDisabledParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
  order?: string;
  userId?: number;
  username?: string;
  email?: string;
}

interface GetFolloweesParams {
  userId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetFollowersParams {
  userId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetFollowingArticlesParams {
  userId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetFollowingQuestionsParams {
  userId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetFollowingTopicsParams {
  userId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
  userId?: number;
  username?: string;
  email?: string;
}

interface GetMineParams {
  include?: Array<string>;
}

interface GetMyAnswersParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetMyArticlesParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetMyCommentsParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetMyFolloweesParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetMyFollowersParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetMyFollowingArticlesParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetMyFollowingQuestionsParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetMyFollowingTopicsParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetMyQuestionsParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetQuestionsParams {
  userId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface RegisterParams {
  userRegisterRequestBody: UserRegisterRequestBody;
}

interface SendPasswordResetEmailParams {
  userSendEmailRequestBody: UserSendEmailRequestBody;
}

interface SendRegisterEmailParams {
  userSendEmailRequestBody: UserSendEmailRequestBody;
}

interface UpdateParams {
  userId: number;
  userRequestBody: UserRequestBody;
  include?: Array<string>;
}

interface UpdateMineParams {
  userRequestBody: UserRequestBody;
  include?: Array<string>;
}

interface UpdatePasswordParams {
  userPasswordResetRequestBody: UserPasswordResetRequestBody;
}

interface UploadMyAvatarParams {
  userAvatarRequestBody: UserAvatarRequestBody;
}

interface UploadMyCoverParams {
  userCoverRequestBody: UserCoverRequestBody;
}

/**
 * UserApi
 */
export default {
  /**
   * 添加关注
   * @param params.userId 用户ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.addFollow',
        '/users/{user_id}/followers',
        params,
        [],
      );

    return post(url);
  },

  /**
   * 🔐删除指定用户的头像，并重置为默认头像
   * 仅管理员可调用该接口
   * @param params.userId 用户ID
   */
  deleteAvatar: (params: DeleteAvatarParams): Promise<UserAvatarResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.deleteAvatar',
        '/users/{user_id}/avatar',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐删除指定用户的封面，并重置为默认封面
   * 仅管理员可调用该接口
   * @param params.userId 用户ID
   */
  deleteCover: (params: DeleteCoverParams): Promise<UserCoverResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.deleteCover',
        '/users/{user_id}/cover',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 取消关注
   * @param params.userId 用户ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.deleteFollow',
        '/users/{user_id}/followers',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 删除当前登录用户的头像，并重置为默认头像
   */
  deleteMyAvatar: (): Promise<UserAvatarResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.deleteMyAvatar', '/user/avatar', {}, []);

    return del(url);
  },

  /**
   * 删除当前登录用户的封面，并重置为默认封面
   */
  deleteMyCover: (): Promise<UserCoverResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.deleteMyCover', '/user/cover', {}, []);

    return del(url);
  },

  /**
   * 🔐禁用指定用户
   * 仅管理员可调用该接口
   * @param params.userId 用户ID
   */
  disable: (params: DisableParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.disable', '/users/{user_id}', params, []);

    return del(url);
  },

  /**
   * 🔐批量禁用用户
   * 仅管理员可调用该接口。只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.userId 用“,”分隔的用户ID，最多可提供100个ID
   */
  disableMultiple: (params: DisableMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.disableMultiple', '/users', params, ['user_id']);

    return del(url);
  },

  /**
   * 🔐恢复指定用户
   * 仅管理员可调用该接口。
   * @param params.userId 用户ID
   */
  enable: (params: EnableParams): Promise<UserResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.enable', '/trash/users/{user_id}', params, []);

    return post(url);
  },

  /**
   * 🔐批量恢复用户
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被启用，该接口都会返回成功。
   * @param params.userId 用“,”分隔的用户ID，最多可提供100个ID
   */
  enableMultiple: (params: EnableMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.enableMultiple', '/trash/users', params, [
        'user_id',
      ]);

    return post(url);
  },

  /**
   * 获取指定用户信息
   * 若是管理员调用该接口、或当前登录用户读取自己的个人信息，将返回用户的所有信息。 其他情况仅返回部分字段（去掉了隐私信息，隐私字段已用 🔐 标明）  &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.userId 用户ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<UserResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.get', '/users/{user_id}', params, ['include']);

    return get(url);
  },

  /**
   * 获取指定用户发表的回答
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getAnswers: (params: GetAnswersParams): Promise<AnswersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getAnswers',
        '/users/{user_id}/answers',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定用户发表的文章
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getArticles: (params: GetArticlesParams): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getArticles',
        '/users/{user_id}/articles',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定用户发表的评论
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getComments',
        '/users/{user_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 🔐获取已禁用用户列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;create_time&#x60;、&#x60;delete_time&#x60;、&#x60;follower_count&#x60;，默认为 &#x60;-delete_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.userId 用户ID
   * @param params.username 用户名
   * @param params.email 邮箱
   */
  getDisabled: (params: GetDisabledParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getDisabled', '/trash/users', params, [
        'page',
        'per_page',
        'include',
        'order',
        'user_id',
        'username',
        'email',
      ]);

    return get(url);
  },

  /**
   * 获取指定用户关注的用户列表
   * &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowees: (params: GetFolloweesParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getFollowees',
        '/users/{user_id}/followees',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定用户的关注者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getFollowers',
        '/users/{user_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定用户关注的文章列表
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowingArticles: (
    params: GetFollowingArticlesParams,
  ): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getFollowingArticles',
        '/users/{user_id}/following_articles',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定用户关注的提问列表
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowingQuestions: (
    params: GetFollowingQuestionsParams,
  ): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getFollowingQuestions',
        '/users/{user_id}/following_questions',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定用户关注的话题列表
   * &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowingTopics: (
    params: GetFollowingTopicsParams,
  ): Promise<TopicsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getFollowingTopics',
        '/users/{user_id}/following_topics',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取用户列表
   * 不包含已禁用的用户。仅管理员可使用 email 参数进行搜索。  可排序字段包括 &#x60;create_time&#x60;、&#x60;follower_count&#x60;，默认为 &#x60;create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.userId 用户ID
   * @param params.username 用户名
   * @param params.email 邮箱
   */
  getList: (params: GetListParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getList', '/users', params, [
        'page',
        'per_page',
        'order',
        'include',
        'user_id',
        'username',
        'email',
      ]);

    return get(url);
  },

  /**
   * 获取当前登录用户的信息
   * &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMine: (params: GetMineParams): Promise<UserResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMine', '/user', params, ['include']);

    return get(url);
  },

  /**
   * 获取当前登录用户发表的回答
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyAnswers: (params: GetMyAnswersParams): Promise<AnswersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMyAnswers', '/user/answers', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取当前登录用户发表的文章
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyArticles: (params: GetMyArticlesParams): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMyArticles', '/user/articles', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取当前登录用户发表的评论
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyComments: (params: GetMyCommentsParams): Promise<CommentsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMyComments', '/user/comments', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取当前登录用户关注的用户
   * &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyFollowees: (params: GetMyFolloweesParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMyFollowees', '/user/followees', params, [
        'page',
        'per_page',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取当前登录用户的关注者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyFollowers: (params: GetMyFollowersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMyFollowers', '/user/followers', params, [
        'page',
        'per_page',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取登录用户关注的文章
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyFollowingArticles: (
    params: GetMyFollowingArticlesParams,
  ): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getMyFollowingArticles',
        '/user/following_articles',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取登录用户关注的提问
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyFollowingQuestions: (
    params: GetMyFollowingQuestionsParams,
  ): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getMyFollowingQuestions',
        '/user/following_questions',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取登录用户关注的话题
   * &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyFollowingTopics: (
    params: GetMyFollowingTopicsParams,
  ): Promise<TopicsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getMyFollowingTopics',
        '/user/following_topics',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取登录用户发表的提问
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getMyQuestions: (
    params: GetMyQuestionsParams,
  ): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.getMyQuestions', '/user/questions', params, [
        'page',
        'per_page',
        'order',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取指定用户发表的提问
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.userId 用户ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getQuestions: (params: GetQuestionsParams): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.getQuestions',
        '/users/{user_id}/questions',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 验证邮箱并创建账号
   * 返回用户信息
   * @param params.userRegisterRequestBody
   */
  register: (params: RegisterParams): Promise<UserResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.register', '/users', params, []);

    return post(url, params.userRegisterRequestBody || {});
  },

  /**
   * 发送重置密码邮箱验证码
   * 若返回参数中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   * @param params.userSendEmailRequestBody
   */
  sendPasswordResetEmail: (
    params: SendPasswordResetEmailParams,
  ): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.sendPasswordResetEmail',
        '/user/password/email',
        params,
        [],
      );

    return post(url, params.userSendEmailRequestBody || {});
  },

  /**
   * 发送注册邮箱验证码
   * 若返回信息中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   * @param params.userSendEmailRequestBody
   */
  sendRegisterEmail: (
    params: SendRegisterEmailParams,
  ): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'UserApi.sendRegisterEmail',
        '/user/register/email',
        params,
        [],
      );

    return post(url, params.userSendEmailRequestBody || {});
  },

  /**
   * 🔐更新指定用户信息
   * 仅管理员可调用该接口  &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.userId 用户ID
   * @param params.userRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  update: (params: UpdateParams): Promise<UserResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.update', '/users/{user_id}', params, [
        'include',
      ]);

    return patch(url, params.userRequestBody || {});
  },

  /**
   * 更新当前登录用户信息
   * &#x60;include&#x60; 参数取值包括：&#x60;is_me&#x60;、&#x60;is_following&#x60;、&#x60;is_followed&#x60;
   * @param params.userRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  updateMine: (params: UpdateMineParams): Promise<UserResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.updateMine', '/user', params, ['include']);

    return patch(url, params.userRequestBody || {});
  },

  /**
   * 验证邮箱并更新密码
   * @param params.userPasswordResetRequestBody
   */
  updatePassword: (params: UpdatePasswordParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.updatePassword', '/user/password', params, []);

    return put(url, params.userPasswordResetRequestBody || {});
  },

  /**
   * 上传当前登录用户的头像
   * @param params.userAvatarRequestBody
   */
  uploadMyAvatar: (
    params: UploadMyAvatarParams,
  ): Promise<UserAvatarResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.uploadMyAvatar', '/user/avatar', params, []);

    return post(url, params.userAvatarRequestBody || {});
  },

  /**
   * 上传当前登录用户的封面
   * @param params.userCoverRequestBody
   */
  uploadMyCover: (params: UploadMyCoverParams): Promise<UserCoverResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('UserApi.uploadMyCover', '/user/cover', params, []);

    return post(url, params.userCoverRequestBody || {});
  },
};
