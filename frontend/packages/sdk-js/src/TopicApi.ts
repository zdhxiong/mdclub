import { getRequest, postRequest, deleteRequest } from './util/requestAlias';
import { buildURL } from './util/requestHandler';
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
  cover: File;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface DeleteFollowParams {
  /**
   * 话题ID
   */
  topic_id: number;
}

interface DeleteMultipleParams {
  /**
   * 多个用 `,` 分隔的话题ID，最多可提供 100 个 ID
   */
  topic_ids: string;
}

interface GetParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
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
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
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
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
  /**
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `topic_id`、`follower_count`、`delete_time`。默认为 `topic_id`。其中 `delete_time` 值仅管理员使用有效。
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
   * 是否仅获取回收站中的数据
   */
  trashed?: boolean;
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

interface TrashParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface TrashMultipleParams {
  /**
   * 多个用 `,` 分隔的话题ID，最多可提供 100 个 ID
   */
  topic_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface UntrashParams {
  /**
   * 话题ID
   */
  topic_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface UntrashMultipleParams {
  /**
   * 多个用 `,` 分隔的话题ID，最多可提供 100 个 ID
   */
  topic_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

interface UpdateParams {
  /**
   * 话题ID
   */
  topic_id: number;
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
  cover?: File;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `is_following`
   */
  include?: Array<'is_following'>;
}

/**
 * 🔐删除话题
 * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/topics/{topic_id}', params));

/**
 * 关注指定话题
 * 关注指定话题
 */
export const addFollow = (
  params: AddFollowParams,
): Promise<FollowerCountResponse> =>
  postRequest(buildURL('/topics/{topic_id}/followers', params));

/**
 * 🔐发布话题
 * 仅管理员可调用该接口
 */
export const create = (params: CreateParams): Promise<TopicResponse> => {
  const formData = new FormData();
  formData.append('name', params.name);
  formData.append('description', params.name);
  formData.append('cover', params.cover);

  return postRequest(buildURL('/topics', params, ['include']), formData);
};

/**
 * 取消关注指定话题
 * 取消关注指定话题
 */
export const deleteFollow = (
  params: DeleteFollowParams,
): Promise<FollowerCountResponse> =>
  deleteRequest(buildURL('/topics/{topic_id}/followers', params));

/**
 * 🔐批量删除话题
 * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/topics/{topic_ids}', params));

/**
 * 获取指定话题信息
 * 获取指定话题信息
 */
export const get = (params: GetParams): Promise<TopicResponse> =>
  getRequest(buildURL('/topics/{topic_id}', params, ['include']));

/**
 * 获取指定话题下的文章
 * 获取指定话题下的文章。
 */
export const getArticles = (
  params: GetArticlesParams,
): Promise<ArticlesResponse> =>
  getRequest(
    buildURL('/topics/{topic_id}/articles', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定话题的关注者
 * 不含已禁用的用户
 */
export const getFollowers = (
  params: GetFollowersParams,
): Promise<UsersResponse> =>
  getRequest(
    buildURL('/topics/{topic_id}/followers', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取全部话题
 * 获取全部话题。
 */
export const getList = (params: GetListParams = {}): Promise<TopicsResponse> =>
  getRequest(
    buildURL('/topics', params, [
      'page',
      'per_page',
      'include',
      'order',
      'topic_id',
      'name',
      'trashed',
    ]),
  );

/**
 * 获取指定话题下的提问
 * 获取指定话题下的提问。
 */
export const getQuestions = (
  params: GetQuestionsParams,
): Promise<QuestionsResponse> =>
  getRequest(
    buildURL('/topics/{topic_id}/questions', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 🔐把话题放入回收站
 * 仅管理员可调用该接口
 */
export const trash = (params: TrashParams): Promise<TopicResponse> =>
  postRequest(buildURL('/topics/{topic_id}/trash', params, ['include']));

/**
 * 🔐批量把话题放入回收站
 * 仅管理员可调用该接口。
 */
export const trashMultiple = (
  params: TrashMultipleParams,
): Promise<TopicsResponse> =>
  postRequest(buildURL('/topics/{topic_ids}/trash', params, ['include']));

/**
 * 🔐把话题移出回收站
 * 仅管理员可调用该接口。
 */
export const untrash = (params: UntrashParams): Promise<TopicResponse> =>
  postRequest(buildURL('/topics/{topic_id}/untrash', params, ['include']));

/**
 * 🔐批量把话题移出回收站
 * 仅管理员可调用该接口。
 */
export const untrashMultiple = (
  params: UntrashMultipleParams,
): Promise<TopicsResponse> =>
  postRequest(buildURL('/topics/{topic_ids}/untrash', params, ['include']));

/**
 * 🔐更新话题信息
 * **仅管理员可调用该接口**  因为 formData 类型的数据只能通过 post 请求提交，所以这里不用 patch 请求
 */
export const update = (params: UpdateParams): Promise<TopicResponse> => {
  const formData = new FormData();
  formData.append('topic_id', params.topic_id.toString());
  params.name && formData.append('name', params.name);
  params.description && formData.append('description', params.description);
  params.cover && formData.append('cover', params.cover);

  return postRequest(
    buildURL('/topics/{topic_id}', params, ['include']),
    formData,
  );
};
