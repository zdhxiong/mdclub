import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  QuestionsResponse,
  TopicsResponse,
  TopicRequestBody,
  UsersResponse,
  TopicResponse,
  EmptyResponse,
  FollowerCountResponse,
  ArticlesResponse,
} from './models';

interface DeleteParams {
  topicId: number;
  force?: '1';
}

interface AddFollowParams {
  topicId: number;
}

interface CreateParams {
  topicRequestBody: TopicRequestBody;
}

interface DeleteFollowParams {
  topicId: number;
}

interface DeleteMultipleParams {
  topicId?: Array<number>;
  force?: '1';
}

interface DestroyParams {
  topicId: number;
}

interface DestroyMultipleParams {
  topicId?: Array<number>;
}

interface GetParams {
  topicId: number;
  include?: Array<string>;
}

interface GetArticlesParams {
  topicId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  perPage?: number;
  order?: string;
  topicId?: number;
  name?: string;
  include?: Array<string>;
}

interface GetFollowersParams {
  topicId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
  order?: string;
  topicId?: number;
  name?: string;
}

interface GetQuestionsParams {
  topicId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface RestoreParams {
  topicId: number;
  include?: Array<string>;
}

interface RestoreMultipleParams {
  topicId?: Array<number>;
}

interface UpdateParams {
  topicId: number;
  topicRequestBody: TopicRequestBody;
  include?: Array<string>;
}

/**
 * TopicApi
 */
export default {
  /**
   * 🔐删除话题
   * 仅管理员可调用该接口 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。  删除后，话题默认进入回收站，可在回收站中恢复该话题。
   * @param params.topicId 话题ID
   * @param params.force 🔐 若该参数为 1，则直接删除，不放入回收站。
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.del', '/topics/{topic_id}', params, ['force']);

    return del(url);
  },

  /**
   * 关注指定话题
   * @param params.topicId 话题ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'TopicApi.addFollow',
        '/topics/{topic_id}/followers',
        params,
        [],
      );

    return post(url);
  },

  /**
   * 🔐发布话题
   * 仅管理员可调用该接口
   * @param params.topicRequestBody
   */
  create: (params: CreateParams): Promise<TopicResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.create', '/topics', params, []);

    return post(url, params.topicRequestBody || {});
  },

  /**
   * 取消关注指定话题
   * @param params.topicId 话题ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'TopicApi.deleteFollow',
        '/topics/{topic_id}/followers',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除话题
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。  删除后，话题默认进入回收站，可在回收站中恢复话题。
   * @param params.topicId 用“,”分隔的话题ID，最多可提供100个ID
   * @param params.force 🔐 若该参数为 1，则直接删除，不放入回收站。
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.deleteMultiple', '/topics', params, [
        'topic_id',
        'force',
      ]);

    return del(url);
  },

  /**
   * 🔐删除指定话题
   * 仅管理员可调用该接口。
   * @param params.topicId 话题ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'TopicApi.destroy',
        '/trash/topics/{topic_id}',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除回收站中的话题
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 topic_id 参数，则将清空回收站中的所有话题。
   * @param params.topicId 用“,”分隔的话题ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.destroyMultiple', '/trash/topics', params, [
        'topic_id',
      ]);

    return del(url);
  },

  /**
   * 获取指定话题信息
   * &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.topicId 话题ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<TopicResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.get', '/topics/{topic_id}', params, [
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取指定话题下的文章
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.topicId 话题ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getArticles: (params: GetArticlesParams): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'TopicApi.getArticles',
        '/topics/{topic_id}/articles',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 🔐获取回收站中的话题列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;topic_id&#x60;、&#x60;follower_count&#x60;、&#x60;delete_time&#x60; 默认为 &#x60;-delete_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.topicId 话题ID
   * @param params.name 话题名称
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getDeleted: (params: GetDeletedParams): Promise<TopicsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.getDeleted', '/trash/topics', params, [
        'page',
        'per_page',
        'order',
        'topic_id',
        'name',
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取指定话题的关注者
   * 不含已禁用的用户  &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.topicId 话题ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'TopicApi.getFollowers',
        '/topics/{topic_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取全部话题
   * 可排序字段包括 &#x60;topic_id&#x60;、&#x60;follower_count&#x60; 默认为 &#x60;topic_id&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.topicId 话题ID
   * @param params.name 话题名称
   */
  getList: (params: GetListParams): Promise<TopicsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.getList', '/topics', params, [
        'page',
        'per_page',
        'include',
        'order',
        'topic_id',
        'name',
      ]);

    return get(url);
  },

  /**
   * 获取指定话题下的提问
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.topicId 话题ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getQuestions: (params: GetQuestionsParams): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'TopicApi.getQuestions',
        '/topics/{topic_id}/questions',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 🔐恢复指定话题
   * 仅管理员可调用该接口。  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.topicId 话题ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  restore: (params: RestoreParams): Promise<TopicResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.restore', '/trash/topics/{topic_id}', params, [
        'include',
      ]);

    return post(url);
  },

  /**
   * 🔐批量恢复话题
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.topicId 用“,”分隔的话题ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.restoreMultiple', '/trash/topics', params, [
        'topic_id',
      ]);

    return post(url);
  },

  /**
   * 🔐更新话题信息
   * **仅管理员可调用该接口**  因为 formData 类型的数据只能通过 post 请求提交，所以这里不用 patch 请求  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.topicId 话题ID
   * @param params.topicRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  update: (params: UpdateParams): Promise<TopicResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('TopicApi.update', '/topics/{topic_id}', params, [
        'include',
      ]);

    return post(url, params.topicRequestBody || {});
  },
};
