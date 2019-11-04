import { get, post, patch, del } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import {
  CommentResponse,
  VoteCountResponse,
  UsersResponse,
  EmptyResponse,
  CommentsResponse,
  VoteRequestBodyTypeEnum,
} from './models';

interface DeleteParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  force?: boolean;
}

interface AddVoteParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 投票类型
   */
  type: VoteRequestBodyTypeEnum;
}

interface DeleteMultipleParams {
  /**
   * 用“,”分隔的提问ID，最多可提供100个ID
   */
  comment_id?: Array<number>;
  /**
   * 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  force?: boolean;
}

interface DeleteVoteParams {
  /**
   * 评论ID
   */
  comment_id: number;
}

interface DestroyParams {
  /**
   * 评论ID
   */
  comment_id: number;
}

interface DestroyMultipleParams {
  /**
   * 用“,”分隔的提问ID，最多可提供100个ID
   */
  comment_id?: Array<number>;
}

interface GetParams {
  /**
   * 评论ID
   */
  comment_id: number;
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
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`delete_time`。默认为 `-delete_time`
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'delete_time'
    | '-vote_count'
    | '-create_time'
    | '-delete_time';
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
  /**
   * 评论ID
   */
  comment_id?: number;
  /**
   * 评论目标的ID
   */
  commentable_id?: number;
  /**
   * 评论目标类型
   */
  commentable_type?: 'article' | 'question' | 'answer';
  /**
   * 用户ID
   */
  user_id?: number;
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
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`。默认为 `-create_time`
   */
  order?: 'vote_count' | 'create_time' | '-vote_count' | '-create_time';
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
  /**
   * 评论ID
   */
  comment_id?: number;
  /**
   * 评论目标的ID
   */
  commentable_id?: number;
  /**
   * 评论目标类型
   */
  commentable_type?: 'article' | 'question' | 'answer';
  /**
   * 用户ID
   */
  user_id?: number;
}

interface GetVotersParams {
  /**
   * 评论ID
   */
  comment_id: number;
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
   * 评论ID
   */
  comment_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface RestoreMultipleParams {
  /**
   * 用“,”分隔的提问ID，最多可提供100个ID
   */
  comment_id?: Array<number>;
}

interface UpdateParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
  /**
   * 评论内容
   */
  content: string;
}

const className = 'CommentApi';

/**
 * CommentApi
 */
export default {
  /**
   * 删除评论
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除评论，默认为软删除，指定 force&#x3D;1 为硬删除。评论被删除后，进入回收站。管理员可在后台恢复评论。  评论作者是否可删除评论，由管理员在后台的设置决定。评论作者删除评论时只能硬删除，删除后不可恢复。
   * @param params.comment_id 评论ID
   * @param params.force 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  del: (params: DeleteParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.del`, '/comments/{comment_id}', params, ['force']),
    ),

  /**
   * 为评论投票
   * 为评论投票
   * @param params.comment_id 评论ID
   * @param params.VoteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> =>
    post(
      buildURL(`${className}.addVote`, '/comments/{comment_id}/voters', params),
      buildRequestBody(params, ['type']),
    ),

  /**
   * 🔐批量删除评论
   * 仅管理员可批量删除评论。  只要没有错误异常，无论是否有评论被删除，该接口都会返回成功。  评论被删除后，默认进入回收站。管理员可在后台恢复评论。
   * @param params.comment_id 用“,”分隔的提问ID，最多可提供100个ID
   * @param params.force 🔐 若该参数为 true，则直接删除，不放入回收站。
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.deleteMultiple`, '/comments', params, [
        'comment_id',
        'force',
      ]),
    ),

  /**
   * 取消为评论的投票
   * 取消为评论的投票
   * @param params.comment_id 评论ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> =>
    del(
      buildURL(
        `${className}.deleteVote`,
        '/comments/{comment_id}/voters',
        params,
      ),
    ),

  /**
   * 🔐删除回收站中的指定评论
   * 仅管理员可调用该接口。
   * @param params.comment_id 评论ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.destroy`, '/trash/comments/{comment_id}', params),
    ),

  /**
   * 🔐批量删除回收站中的评论
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.comment_id 用“,”分隔的提问ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.destroyMultiple`, '/trash/comments', params, [
        'comment_id',
      ]),
    ),

  /**
   * 获取评论详情
   * 获取评论详情
   * @param params.comment_id 评论ID
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  get: (params: GetParams): Promise<CommentResponse> =>
    get(
      buildURL(`${className}.get`, '/comments/{comment_id}', params, [
        'include',
      ]),
    ),

  /**
   * 🔐获取回收站中的评论列表
   * 仅管理员可调用该接口。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;delete_time&#x60;。默认为 &#x60;-delete_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   * @param params.comment_id 评论ID
   * @param params.commentable_id 评论目标的ID
   * @param params.commentable_type 评论目标类型
   * @param params.user_id 用户ID
   */
  getDeleted: (params: GetDeletedParams): Promise<CommentsResponse> =>
    get(
      buildURL(`${className}.getDeleted`, '/trash/comments', params, [
        'page',
        'per_page',
        'order',
        'include',
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
      ]),
    ),

  /**
   * 获取所有评论
   * 获取所有评论。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   * @param params.comment_id 评论ID
   * @param params.commentable_id 评论目标的ID
   * @param params.commentable_type 评论目标类型
   * @param params.user_id 用户ID
   */
  getList: (params: GetListParams): Promise<CommentsResponse> =>
    get(
      buildURL(`${className}.getList`, '/comments', params, [
        'page',
        'per_page',
        'order',
        'include',
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
      ]),
    ),

  /**
   * 获取评论的投票者
   * 获取评论的投票者
   * @param params.comment_id 评论ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> =>
    get(
      buildURL(
        `${className}.getVoters`,
        '/comments/{comment_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      ),
    ),

  /**
   * 🔐恢复指定评论
   * 仅管理员可调用该接口。
   * @param params.comment_id 评论ID
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  restore: (params: RestoreParams): Promise<CommentResponse> =>
    post(
      buildURL(`${className}.restore`, '/trash/comments/{comment_id}', params, [
        'include',
      ]),
    ),

  /**
   * 🔐批量恢复评论
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.comment_id 用“,”分隔的提问ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> =>
    post(
      buildURL(`${className}.restoreMultiple`, '/trash/comments', params, [
        'comment_id',
      ]),
    ),

  /**
   * 修改评论
   * 管理员可修改评论。评论作者是否可修改评论，由管理员在后台的设置决定。
   * @param params.comment_id 评论ID
   * @param params.CommentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  update: (params: UpdateParams): Promise<CommentResponse> =>
    patch(
      buildURL(`${className}.update`, '/comments/{comment_id}', params, [
        'include',
      ]),
      buildRequestBody(params, ['content']),
    ),
};
