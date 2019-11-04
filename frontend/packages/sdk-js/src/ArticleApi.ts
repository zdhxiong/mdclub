import { get, post, patch, del } from './util/requestAlias';
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
   * 包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
  /**
   * 标题
   */
  title: string;
  /**
   * 话题ID，多个ID用“,”分隔，最多支持 10 个ID
   */
  topic_id: string;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

interface CreateCommentParams {
  /**
   * 文章ID
   */
  article_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
  /**
   * 评论内容
   */
  content: string;
}

interface DeleteFollowParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface DeleteMultipleParams {
  /**
   * 用“,”分隔的文章ID，最多可提供100个ID
   */
  article_id?: Array<number>;
}

interface DeleteVoteParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface DestroyParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface DestroyMultipleParams {
  /**
   * 用“,”分隔的话题ID，最多可提供100个ID
   */
  topic_id?: Array<number>;
}

interface GetParams {
  /**
   * 文章ID
   */
  article_id: number;
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
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`。默认为 `-create_time`
   */
  order?: 'vote_count' | 'create_time' | '-vote_count' | '-create_time';
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
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
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`、`delete_time`。默认为 `-delete_time`
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
   * 包含的关联数据，用“,”分隔。可以为 `is_followed`, `is_following`, `is_me`
   */
  include?: Array<'is_followed' | 'is_following' | 'is_me'>;
  /**
   * 默认获取全部投票类型的用户 `up` 表示仅获取投赞成票的用户 `down` 表示仅获取投反对票的用户
   */
  type?: 'up' | 'down';
}

interface RestoreParams {
  /**
   * 文章ID
   */
  article_id: number;
}

interface RestoreMultipleParams {
  /**
   * 用“,”分隔的文章ID，最多可提供100个ID
   */
  article_id?: Array<number>;
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
   * 话题ID，多个ID用“,”分隔，最多支持 10 个ID
   */
  topic_id?: string;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

const className = 'ArticleApi';

/**
 * ArticleApi
 */
export default {
  /**
   * 删除指定文章
   * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。  文章被删除后，进入回收站。管理员可在后台恢复文章。
   * @param params.article_id 文章ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> =>
    del(buildURL(`${className}.del`, '/articles/{article_id}', params)),

  /**
   * 添加关注
   * 添加关注
   * @param params.article_id 文章ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> =>
    post(
      buildURL(
        `${className}.addFollow`,
        '/articles/{article_id}/followers',
        params,
      ),
    ),

  /**
   * 为文章投票
   * 为文章投票
   * @param params.article_id 文章ID
   * @param params.VoteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> =>
    post(
      buildURL(`${className}.addVote`, '/articles/{article_id}/voters', params),
      buildRequestBody(params, ['type']),
    ),

  /**
   * 发表文章
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.ArticleCreateRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  create: (params: CreateParams): Promise<ArticleResponse> =>
    post(
      buildURL(`${className}.create`, '/articles', params, ['include']),
      buildRequestBody(params, [
        'title',
        'topic_id',
        'content_markdown',
        'content_rendered',
      ]),
    ),

  /**
   * 在指定文章下发表评论
   * 在指定文章下发表评论
   * @param params.article_id 文章ID
   * @param params.CommentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  createComment: (params: CreateCommentParams): Promise<CommentResponse> =>
    post(
      buildURL(
        `${className}.createComment`,
        '/articles/{article_id}/comments',
        params,
        ['include'],
      ),
      buildRequestBody(params, ['content']),
    ),

  /**
   * 取消关注
   * 取消关注
   * @param params.article_id 文章ID
   */
  deleteFollow: (params: DeleteFollowParams): Promise<FollowerCountResponse> =>
    del(
      buildURL(
        `${className}.deleteFollow`,
        '/articles/{article_id}/followers',
        params,
      ),
    ),

  /**
   * 🔐批量删除文章
   * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。  文章被删除后，进入回收站。管理员可在后台恢复文章。
   * @param params.article_id 用“,”分隔的文章ID，最多可提供100个ID
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.deleteMultiple`, '/articles', params, [
        'article_id',
      ]),
    ),

  /**
   * 取消为文章的投票
   * 取消为文章的投票
   * @param params.article_id 文章ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> =>
    del(
      buildURL(
        `${className}.deleteVote`,
        '/articles/{article_id}/voters',
        params,
      ),
    ),

  /**
   * 🔐删除指定文章
   * 仅管理员可调用该接口。
   * @param params.article_id 文章ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.destroy`, '/trash/articles/{article_id}', params),
    ),

  /**
   * 🔐批量删除回收站中的话题
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 topic_id 参数，则将清空回收站中的所有文章。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.destroyMultiple`, '/trash/articles', params, [
        'topic_id',
      ]),
    ),

  /**
   * 获取指定文章信息
   * 获取指定文章信息
   * @param params.article_id 文章ID
   */
  get: (params: GetParams): Promise<ArticleResponse> =>
    get(buildURL(`${className}.get`, '/articles/{article_id}', params)),

  /**
   * 获取指定文章的评论列表
   * 获取指定文章的评论列表。
   * @param params.article_id 文章ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> =>
    get(
      buildURL(
        `${className}.getComments`,
        '/articles/{article_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    ),

  /**
   * 🔐获取回收站中的文章列表
   * 仅管理员可调用该接口。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;。默认为 &#x60;-delete_time&#x60;
   * @param params.article_id 文章ID
   * @param params.user_id 用户ID
   * @param params.topic_id 话题ID
   */
  getDeleted: (params: GetDeletedParams): Promise<ArticlesResponse> =>
    get(
      buildURL(`${className}.getDeleted`, '/trash/articles', params, [
        'page',
        'per_page',
        'order',
        'article_id',
        'user_id',
        'topic_id',
      ]),
    ),

  /**
   * 获取指定文章的关注者
   * 获取指定文章的关注者
   * @param params.article_id 文章ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> =>
    get(
      buildURL(
        `${className}.getFollowers`,
        '/articles/{article_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      ),
    ),

  /**
   * 获取文章列表
   * 获取文章列表。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   * @param params.article_id 文章ID
   * @param params.user_id 用户ID
   * @param params.topic_id 话题ID
   */
  getList: (params: GetListParams): Promise<ArticlesResponse> =>
    get(
      buildURL(`${className}.getList`, '/articles', params, [
        'page',
        'per_page',
        'order',
        'include',
        'article_id',
        'user_id',
        'topic_id',
      ]),
    ),

  /**
   * 获取文章的投票者
   * 获取文章的投票者
   * @param params.article_id 文章ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> =>
    get(
      buildURL(
        `${className}.getVoters`,
        '/articles/{article_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      ),
    ),

  /**
   * 🔐恢复指定文章
   * 仅管理员可调用该接口。
   * @param params.article_id 文章ID
   */
  restore: (params: RestoreParams): Promise<ArticleResponse> =>
    post(
      buildURL(`${className}.restore`, '/trash/articles/{article_id}', params),
    ),

  /**
   * 🔐批量恢复文章
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.article_id 用“,”分隔的文章ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> =>
    post(
      buildURL(`${className}.restoreMultiple`, '/trash/articles', params, [
        'article_id',
      ]),
    ),

  /**
   * 更新文章信息
   * 管理员可修改文章。文章作者是否可修改文章，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.article_id 文章ID
   * @param params.ArticleUpdateRequestBody
   */
  update: (params: UpdateParams): Promise<ArticleResponse> =>
    patch(
      buildURL(`${className}.update`, '/articles/{article_id}', params),
      buildRequestBody(params, [
        'title',
        'topic_id',
        'content_markdown',
        'content_rendered',
      ]),
    ),
};
