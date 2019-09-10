import { get, post, del } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import {
  QuestionsResponse,
  TopicsResponse,
  UsersResponse,
  TopicResponse,
  EmptyResponse,
  FollowerCountResponse,
  ArticlesResponse,
} from './models';

interface DeleteParams {
  topic_id: number;
  force?: '1';
}

interface AddFollowParams {
  topic_id: number;
}

interface CreateParams {
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

interface DeleteFollowParams {
  topic_id: number;
}

interface DeleteMultipleParams {
  topic_id?: Array<number>;
  force?: '1';
}

interface DestroyParams {
  topic_id: number;
}

interface DestroyMultipleParams {
  topic_id?: Array<number>;
}

interface GetParams {
  topic_id: number;
  include?: Array<string>;
}

interface GetArticlesParams {
  topic_id: number;
  page?: number;
  per_page?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  per_page?: number;
  order?: string;
  topic_id?: number;
  name?: string;
  include?: Array<string>;
}

interface GetFollowersParams {
  topic_id: number;
  page?: number;
  per_page?: number;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  per_page?: number;
  include?: Array<string>;
  order?: string;
  topic_id?: number;
  name?: string;
}

interface GetQuestionsParams {
  topic_id: number;
  page?: number;
  per_page?: number;
  order?: string;
  include?: Array<string>;
}

interface RestoreParams {
  topic_id: number;
  include?: Array<string>;
}

interface RestoreMultipleParams {
  topic_id?: Array<number>;
}

interface UpdateParams {
  topic_id: number;
  include?: Array<string>;

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

const className = 'TopicApi';

/**
 * TopicApi
 */
export default {
  /**
   * 🔐删除话题
   * 仅管理员可调用该接口 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。  删除后，话题默认进入回收站，可在回收站中恢复该话题。
   * @param params.topic_id 话题ID
   * @param params.force 🔐 若该参数为 1，则直接删除，不放入回收站。
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.del`, '/topics/{topic_id}', params, ['force']),
    );
  },

  /**
   * 关注指定话题
   * @param params.topic_id 话题ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    return post(
      buildURL(
        `${className}.addFollow`,
        '/topics/{topic_id}/followers',
        params,
      ),
    );
  },

  /**
   * 🔐发布话题
   * 仅管理员可调用该接口
   * @param params.TopicRequestBody
   */
  create: (params: CreateParams): Promise<TopicResponse> => {
    return post(
      buildURL(`${className}.create`, '/topics', params),
      buildRequestBody(params, ['name', 'description', 'cover']),
    );
  },

  /**
   * 取消关注指定话题
   * @param params.topic_id 话题ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    return del(
      buildURL(
        `${className}.deleteFollow`,
        '/topics/{topic_id}/followers',
        params,
      ),
    );
  },

  /**
   * 🔐批量删除话题
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。  删除后，话题默认进入回收站，可在回收站中恢复话题。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   * @param params.force 🔐 若该参数为 1，则直接删除，不放入回收站。
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.deleteMultiple`, '/topics', params, [
        'topic_id',
        'force',
      ]),
    );
  },

  /**
   * 🔐删除指定话题
   * 仅管理员可调用该接口。
   * @param params.topic_id 话题ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.destroy`, '/trash/topics/{topic_id}', params),
    );
  },

  /**
   * 🔐批量删除回收站中的话题
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 topic_id 参数，则将清空回收站中的所有话题。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.destroyMultiple`, '/trash/topics', params, [
        'topic_id',
      ]),
    );
  },

  /**
   * 获取指定话题信息
   * &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.topic_id 话题ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<TopicResponse> => {
    return get(
      buildURL(`${className}.get`, '/topics/{topic_id}', params, ['include']),
    );
  },

  /**
   * 获取指定话题下的文章
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.topic_id 话题ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getArticles: (params: GetArticlesParams): Promise<ArticlesResponse> => {
    return get(
      buildURL(
        `${className}.getArticles`,
        '/topics/{topic_id}/articles',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 🔐获取回收站中的话题列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;topic_id&#x60;、&#x60;follower_count&#x60;、&#x60;delete_time&#x60; 默认为 &#x60;-delete_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.topic_id 话题ID
   * @param params.name 话题名称
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getDeleted: (params: GetDeletedParams): Promise<TopicsResponse> => {
    return get(
      buildURL(`${className}.getDeleted`, '/trash/topics', params, [
        'page',
        'per_page',
        'order',
        'topic_id',
        'name',
        'include',
      ]),
    );
  },

  /**
   * 获取指定话题的关注者
   * 不含已禁用的用户  &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.topic_id 话题ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    return get(
      buildURL(
        `${className}.getFollowers`,
        '/topics/{topic_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取全部话题
   * 可排序字段包括 &#x60;topic_id&#x60;、&#x60;follower_count&#x60; 默认为 &#x60;topic_id&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.topic_id 话题ID
   * @param params.name 话题名称
   */
  getList: (params: GetListParams): Promise<TopicsResponse> => {
    return get(
      buildURL(`${className}.getList`, '/topics', params, [
        'page',
        'per_page',
        'include',
        'order',
        'topic_id',
        'name',
      ]),
    );
  },

  /**
   * 获取指定话题下的提问
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.topic_id 话题ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getQuestions: (params: GetQuestionsParams): Promise<QuestionsResponse> => {
    return get(
      buildURL(
        `${className}.getQuestions`,
        '/topics/{topic_id}/questions',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 🔐恢复指定话题
   * 仅管理员可调用该接口。  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.topic_id 话题ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  restore: (params: RestoreParams): Promise<TopicResponse> => {
    return post(
      buildURL(`${className}.restore`, '/trash/topics/{topic_id}', params, [
        'include',
      ]),
    );
  },

  /**
   * 🔐批量恢复话题
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    return post(
      buildURL(`${className}.restoreMultiple`, '/trash/topics', params, [
        'topic_id',
      ]),
    );
  },

  /**
   * 🔐更新话题信息
   * **仅管理员可调用该接口**  因为 formData 类型的数据只能通过 post 请求提交，所以这里不用 patch 请求  &#x60;include&#x60; 参数取值包括：&#x60;is_following&#x60;
   * @param params.topic_id 话题ID
   * @param params.TopicRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  update: (params: UpdateParams): Promise<TopicResponse> => {
    return post(
      buildURL(`${className}.update`, '/topics/{topic_id}', params, [
        'include',
      ]),
      buildRequestBody(params, ['name', 'description', 'cover']),
    );
  },
};
