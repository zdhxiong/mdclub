import {
  getRequest,
  postRequest,
  patchRequest,
  deleteRequest,
} from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import {
  CommentResponse,
  VoteCountResponse,
  ArticleResponse,
  UsersResponse,
  EmptyResponse,
  FollowerCountResponse,
  ArticlesResponse,
  CommentsResponse,
  VoteRequestBodyTypeEnum,
} from './models';

interface DeleteParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface AddFollowParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface AddVoteParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 投票类型
   */
  type: VoteRequestBodyTypeEnum;
}

interface CreateParams {
  /**
   * 标题
   */
  title: string;
  /**
   * 话题ID
   */
  topic_id: Array<number>;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface CreateCommentParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 评论内容
   */
  content: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface DeleteFollowParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface DeleteMultipleParams {
  /**
   * 多个用 `,` 分隔的文章ID，最多可提供 100 个 ID
   */
  article_ids: string;
}

interface DeleteVoteParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface GetParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetCommentsParams {
  /**
   * 文章ID
   */
  article_id: number;
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

interface GetFollowersParams {
  /**
   * 文章ID
   */
  article_id: number;
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
  /**
   * 文章ID
   */
  article_id?: number;
  /**
   * 用户ID
   */
  user_id?: number;
  /**
   * 话题ID
   */
  topic_id?: number;
  /**
   * 是否仅获取回收站中的数据
   */
  trashed?: boolean;
}

interface GetVotersParams {
  /**
   * 文章ID
   */
  article_id: number;
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
  /**
   * 默认获取全部投票类型的用户 `up` 表示仅获取投赞成票的用户 `down` 表示仅获取投反对票的用户
   */
  type?: 'up' | 'down';
}

interface TrashParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface TrashMultipleParams {
  /**
   * 多个用 `,` 分隔的文章ID，最多可提供 100 个 ID
   */
  article_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface UntrashParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface UntrashMultipleParams {
  /**
   * 多个用 `,` 分隔的文章ID，最多可提供 100 个 ID
   */
  article_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface UpdateParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 标题
   */
  title?: string;
  /**
   * 话题ID
   */
  topic_id?: Array<number>;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

const className = 'ArticleApi';

/**
 * 删除文章
 * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。
 * @param params.article_id 文章ID
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL(`${className}.del`, '/articles/{article_id}', params));

/**
 * 添加关注
 * 添加关注
 * @param params.article_id 文章ID
 */
export const addFollow = (
  params: AddFollowParams,
): Promise<FollowerCountResponse> =>
  postRequest(
    buildURL(
      `${className}.addFollow`,
      '/articles/{article_id}/followers',
      params,
    ),
  );

/**
 * 为文章投票
 * 为文章投票
 * @param params.article_id 文章ID
 * @param params.VoteRequestBody
 */
export const addVote = (params: AddVoteParams): Promise<VoteCountResponse> =>
  postRequest(
    buildURL(`${className}.addVote`, '/articles/{article_id}/voters', params),
    buildRequestBody(params, ['type']),
  );

/**
 * 发表文章
 * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
 * @param params.ArticleCreateRequestBody
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const create = (params: CreateParams): Promise<ArticleResponse> =>
  postRequest(
    buildURL(`${className}.create`, '/articles', params, ['include']),
    buildRequestBody(params, [
      'title',
      'topic_id',
      'content_markdown',
      'content_rendered',
    ]),
  );

/**
 * 在指定文章下发表评论
 * 在指定文章下发表评论
 * @param params.article_id 文章ID
 * @param params.CommentRequestBody
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
 */
export const createComment = (
  params: CreateCommentParams,
): Promise<CommentResponse> =>
  postRequest(
    buildURL(
      `${className}.createComment`,
      '/articles/{article_id}/comments',
      params,
      ['include'],
    ),
    buildRequestBody(params, ['content']),
  );

/**
 * 取消关注
 * 取消关注
 * @param params.article_id 文章ID
 */
export const deleteFollow = (
  params: DeleteFollowParams,
): Promise<FollowerCountResponse> =>
  deleteRequest(
    buildURL(
      `${className}.deleteFollow`,
      '/articles/{article_id}/followers',
      params,
    ),
  );

/**
 * 🔐批量删除文章
 * 仅管理员可调用该接口。 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。
 * @param params.article_ids 多个用 &#x60;,&#x60; 分隔的文章ID，最多可提供 100 个 ID
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(
    buildURL(`${className}.deleteMultiple`, '/articles/{article_ids}', params),
  );

/**
 * 取消为文章的投票
 * 取消为文章的投票
 * @param params.article_id 文章ID
 */
export const deleteVote = (
  params: DeleteVoteParams,
): Promise<VoteCountResponse> =>
  deleteRequest(
    buildURL(
      `${className}.deleteVote`,
      '/articles/{article_id}/voters',
      params,
    ),
  );

/**
 * 获取指定文章信息
 * 获取指定文章信息
 * @param params.article_id 文章ID
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const get = (params: GetParams): Promise<ArticleResponse> =>
  getRequest(
    buildURL(`${className}.get`, '/articles/{article_id}', params, ['include']),
  );

/**
 * 获取指定文章的评论列表
 * 获取指定文章的评论列表。
 * @param params.article_id 文章ID
 * @param params.page 当前页数
 * @param params.per_page 每页条数（最大为 100）
 * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;delete_time&#x60;。默认为 &#x60;-create_time&#x60;。其中 &#x60;delete_time&#x60; 值仅管理员使用有效。
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
 */
export const getComments = (
  params: GetCommentsParams,
): Promise<CommentsResponse> =>
  getRequest(
    buildURL(
      `${className}.getComments`,
      '/articles/{article_id}/comments',
      params,
      ['page', 'per_page', 'order', 'include'],
    ),
  );

/**
 * 获取指定文章的关注者
 * 获取指定文章的关注者
 * @param params.article_id 文章ID
 * @param params.page 当前页数
 * @param params.per_page 每页条数（最大为 100）
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
 */
export const getFollowers = (
  params: GetFollowersParams,
): Promise<UsersResponse> =>
  getRequest(
    buildURL(
      `${className}.getFollowers`,
      '/articles/{article_id}/followers',
      params,
      ['page', 'per_page', 'include'],
    ),
  );

/**
 * 获取文章列表
 * 获取文章列表。
 * @param params.page 当前页数
 * @param params.per_page 每页条数（最大为 100）
 * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;。默认为 &#x60;-create_time&#x60;。其中 &#x60;delete_time&#x60; 值仅管理员使用有效。
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 * @param params.article_id 文章ID
 * @param params.user_id 用户ID
 * @param params.topic_id 话题ID
 * @param params.trashed 是否仅获取回收站中的数据
 */
export const getList = (params: GetListParams): Promise<ArticlesResponse> =>
  getRequest(
    buildURL(`${className}.getList`, '/articles', params, [
      'page',
      'per_page',
      'order',
      'include',
      'article_id',
      'user_id',
      'topic_id',
      'trashed',
    ]),
  );

/**
 * 获取文章的投票者
 * 获取文章的投票者
 * @param params.article_id 文章ID
 * @param params.page 当前页数
 * @param params.per_page 每页条数（最大为 100）
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
 * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
 */
export const getVoters = (params: GetVotersParams): Promise<UsersResponse> =>
  getRequest(
    buildURL(
      `${className}.getVoters`,
      '/articles/{article_id}/voters',
      params,
      ['page', 'per_page', 'include', 'type'],
    ),
  );

/**
 * 🔐把文章放入回收站
 * 仅管理员可调用该接口
 * @param params.article_id 文章ID
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const trash = (params: TrashParams): Promise<ArticleResponse> =>
  postRequest(
    buildURL(`${className}.trash`, '/articles/{article_id}/trash', params, [
      'include',
    ]),
  );

/**
 * 🔐批量把文章放入回收站
 * 仅管理员可调用该接口。
 * @param params.article_ids 多个用 &#x60;,&#x60; 分隔的文章ID，最多可提供 100 个 ID
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const trashMultiple = (
  params: TrashMultipleParams,
): Promise<ArticlesResponse> =>
  postRequest(
    buildURL(
      `${className}.trashMultiple`,
      '/articles/{article_ids}/trash',
      params,
      ['include'],
    ),
  );

/**
 * 🔐把文章移出回收站
 * 仅管理员可调用该接口。
 * @param params.article_id 文章ID
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const untrash = (params: UntrashParams): Promise<ArticleResponse> =>
  postRequest(
    buildURL(`${className}.untrash`, '/articles/{article_id}/untrash', params, [
      'include',
    ]),
  );

/**
 * 🔐批量把文章移出回收站
 * 仅管理员可调用该接口。
 * @param params.article_ids 多个用 &#x60;,&#x60; 分隔的文章ID，最多可提供 100 个 ID
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const untrashMultiple = (
  params: UntrashMultipleParams,
): Promise<ArticlesResponse> =>
  postRequest(
    buildURL(
      `${className}.untrashMultiple`,
      '/articles/{article_ids}/untrash',
      params,
      ['include'],
    ),
  );

/**
 * 更新文章信息
 * 管理员可修改文章。文章作者是否可修改文章，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
 * @param params.article_id 文章ID
 * @param params.ArticleUpdateRequestBody
 * @param params.include 响应中需要包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
 */
export const update = (params: UpdateParams): Promise<ArticleResponse> =>
  patchRequest(
    buildURL(`${className}.update`, '/articles/{article_id}', params, [
      'include',
    ]),
    buildRequestBody(params, [
      'title',
      'topic_id',
      'content_markdown',
      'content_rendered',
    ]),
  );
