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
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  force?: boolean;
}

interface AddFollowParams {
  /**
   * 话题ID
   */
  topic_id: number;
}

interface CreateParams {
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

interface DeleteFollowParams {
  /**
   * 话题ID
   */
  topic_id: number;
}

interface DeleteMultipleParams {
  /**
   * 用“,”分隔的话题ID，最多可提供100个ID
   */
  topic_id?: Array<number>;
  /**
   * 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  force?: boolean;
}

interface DestroyParams {
  /**
   * 话题ID
   */
  topic_id: number;
}

interface DestroyMultipleParams {
  /**
   * 用“,”分隔的话题ID，最多可提供100个ID
   */
  topic_id?: Array<number>;
}

interface GetParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface GetArticlesParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`。默认为 `-create_time`
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetDeletedParams {
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `topic_id`、`follower_count`、`delete_time`。默认为 `-delete_time`
   */
  order?:
    | 'topic_id'
    | 'follower_count'
    | 'delete_time'
    | '-topic_id'
    | '-follower_count'
    | '-delete_time';
  /**
   * 话题ID
   */
  topic_id?: number;
  /**
   * 话题名称
   */
  name?: string;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface GetFollowersParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
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
   * 包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `topic_id`、`follower_count`。默认为 `topic_id`
   */
  order?: 'topic_id' | 'follower_count' | '-topic_id' | '-follower_count';
  /**
   * 话题ID
   */
  topic_id?: number;
  /**
   * 话题名称
   */
  name?: string;
}

interface GetQuestionsParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`。默认为 `-create_time`
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface RestoreParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface RestoreMultipleParams {
  /**
   * 用“,”分隔的话题ID，最多可提供100个ID
   */
  topic_id?: Array<number>;
}

interface UpdateParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;

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
   * @param params.force 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  del: (params: DeleteParams): Promise<EmptyResponse> =>
    del(buildURL(`${className}.del`, '/topics/{topic_id}', params, ['force'])),

  /**
   * 关注指定话题
   * 关注指定话题
   * @param params.topic_id 话题ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> =>
    post(
      buildURL(
        `${className}.addFollow`,
        '/topics/{topic_id}/followers',
        params,
      ),
    ),

  /**
   * 🔐发布话题
   * 仅管理员可调用该接口
   * @param params.TopicCreateRequestBody
   */
  create: (params: CreateParams): Promise<TopicResponse> =>
    post(
      buildURL(`${className}.create`, '/topics', params),
      buildRequestBody(params, ['name', 'description', 'cover']),
    ),

  /**
   * 取消关注指定话题
   * 取消关注指定话题
   * @param params.topic_id 话题ID
   */
  deleteFollow: (params: DeleteFollowParams): Promise<FollowerCountResponse> =>
    del(
      buildURL(
        `${className}.deleteFollow`,
        '/topics/{topic_id}/followers',
        params,
      ),
    ),

  /**
   * 🔐批量删除话题
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。  删除后，话题默认进入回收站，可在回收站中恢复话题。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   * @param params.force 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.deleteMultiple`, '/topics', params, [
        'topic_id',
        'force',
      ]),
    ),

  /**
   * 🔐删除指定话题
   * 仅管理员可调用该接口。
   * @param params.topic_id 话题ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> =>
    del(buildURL(`${className}.destroy`, '/trash/topics/{topic_id}', params)),

  /**
   * 🔐批量删除回收站中的话题
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 topic_id 参数，则将清空回收站中的所有话题。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.destroyMultiple`, '/trash/topics', params, [
        'topic_id',
      ]),
    ),

  /**
   * 获取指定话题信息
   * 获取指定话题信息
   * @param params.topic_id 话题ID
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   */
  get: (params: GetParams): Promise<TopicResponse> =>
    get(
      buildURL(`${className}.get`, '/topics/{topic_id}', params, ['include']),
    ),

  /**
   * 获取指定话题下的文章
   * 获取指定话题下的文章。
   * @param params.topic_id 话题ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getArticles: (params: GetArticlesParams): Promise<ArticlesResponse> =>
    get(
      buildURL(
        `${className}.getArticles`,
        '/topics/{topic_id}/articles',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    ),

  /**
   * 🔐获取回收站中的话题列表
   * 仅管理员可调用该接口。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;topic_id&#x60;、&#x60;follower_count&#x60;、&#x60;delete_time&#x60;。默认为 &#x60;-delete_time&#x60;
   * @param params.topic_id 话题ID
   * @param params.name 话题名称
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   */
  getDeleted: (params: GetDeletedParams): Promise<TopicsResponse> =>
    get(
      buildURL(`${className}.getDeleted`, '/trash/topics', params, [
        'page',
        'per_page',
        'order',
        'topic_id',
        'name',
        'include',
      ]),
    ),

  /**
   * 获取指定话题的关注者
   * 不含已禁用的用户
   * @param params.topic_id 话题ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> =>
    get(
      buildURL(
        `${className}.getFollowers`,
        '/topics/{topic_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      ),
    ),

  /**
   * 获取全部话题
   * 获取全部话题。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;topic_id&#x60;、&#x60;follower_count&#x60;。默认为 &#x60;topic_id&#x60;
   * @param params.topic_id 话题ID
   * @param params.name 话题名称
   */
  getList: (params: GetListParams): Promise<TopicsResponse> =>
    get(
      buildURL(`${className}.getList`, '/topics', params, [
        'page',
        'per_page',
        'include',
        'order',
        'topic_id',
        'name',
      ]),
    ),

  /**
   * 获取指定话题下的提问
   * 获取指定话题下的提问。
   * @param params.topic_id 话题ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  getQuestions: (params: GetQuestionsParams): Promise<QuestionsResponse> =>
    get(
      buildURL(
        `${className}.getQuestions`,
        '/topics/{topic_id}/questions',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    ),

  /**
   * 🔐恢复指定话题
   * 仅管理员可调用该接口。
   * @param params.topic_id 话题ID
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   */
  restore: (params: RestoreParams): Promise<TopicResponse> =>
    post(
      buildURL(`${className}.restore`, '/trash/topics/{topic_id}', params, [
        'include',
      ]),
    ),

  /**
   * 🔐批量恢复话题
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> =>
    post(
      buildURL(`${className}.restoreMultiple`, '/trash/topics', params, [
        'topic_id',
      ]),
    ),

  /**
   * 🔐更新话题信息
   * **仅管理员可调用该接口**  因为 formData 类型的数据只能通过 post 请求提交，所以这里不用 patch 请求
   * @param params.topic_id 话题ID
   * @param params.TopicUpdateRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_following&#x60;
   */
  update: (params: UpdateParams): Promise<TopicResponse> =>
    post(
      buildURL(`${className}.update`, '/topics/{topic_id}', params, [
        'include',
      ]),
      buildRequestBody(params, ['name', 'description', 'cover']),
    ),
};
