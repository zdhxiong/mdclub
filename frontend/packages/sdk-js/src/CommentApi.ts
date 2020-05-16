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

interface CreateReplyParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 评论内容
   */
  content: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface DeleteMultipleParams {
  /**
   * 多个用 `,` 分隔的评论ID，最多可提供 100 个 ID
   */
  comment_ids: string;
}

interface DeleteVoteParams {
  /**
   * 评论ID
   */
  comment_id: number;
}

interface GetParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
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
  commentable_type?: 'article' | 'question' | 'answer' | 'comment';
  /**
   * 用户ID
   */
  user_id?: number;
  /**
   * 是否仅获取回收站中的数据
   */
  trashed?: boolean;
}

interface GetRepliesParams {
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
   * 评论ID
   */
  comment_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface TrashMultipleParams {
  /**
   * 多个用 `,` 分隔的评论ID，最多可提供 100 个 ID
   */
  comment_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface UntrashParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface UntrashMultipleParams {
  /**
   * 多个用 `,` 分隔的评论ID，最多可提供 100 个 ID
   */
  comment_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

interface UpdateParams {
  /**
   * 评论ID
   */
  comment_id: number;
  /**
   * 评论内容
   */
  content: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
}

/**
 * 删除评论
 * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除评论。评论作者是否可删除评论，由管理员在后台的设置决定。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/comments/{comment_id}', params));

/**
 * 为评论投票
 * 为评论投票
 */
export const addVote = (params: AddVoteParams): Promise<VoteCountResponse> =>
  postRequest(
    buildURL('/comments/{comment_id}/voters', params),
    buildRequestBody(params, ['type']),
  );

/**
 * 在指定评论下发表回复
 * 在指定评论下发表回复
 */
export const createReply = (
  params: CreateReplyParams,
): Promise<CommentResponse> =>
  postRequest(
    buildURL('/comments/{comment_id}/replies', params, ['include']),
    buildRequestBody(params, ['content']),
  );

/**
 * 🔐批量删除评论
 * 仅管理员可调用该接口。 只要没有错误异常，无论是否有评论被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/comments/{comment_ids}', params));

/**
 * 取消为评论的投票
 * 取消为评论的投票
 */
export const deleteVote = (
  params: DeleteVoteParams,
): Promise<VoteCountResponse> =>
  deleteRequest(buildURL('/comments/{comment_id}/voters', params));

/**
 * 获取评论详情
 * 获取评论详情
 */
export const get = (params: GetParams): Promise<CommentResponse> =>
  getRequest(buildURL('/comments/{comment_id}', params, ['include']));

/**
 * 获取所有评论
 * 获取所有评论。
 */
export const getList = (
  params: GetListParams = {},
): Promise<CommentsResponse> =>
  getRequest(
    buildURL('/comments', params, [
      'page',
      'per_page',
      'order',
      'include',
      'comment_id',
      'commentable_id',
      'commentable_type',
      'user_id',
      'trashed',
    ]),
  );

/**
 * 获取指定评论的回复
 * 获知指定评论的回复。
 */
export const getReplies = (
  params: GetRepliesParams,
): Promise<CommentsResponse> =>
  getRequest(
    buildURL('/comments/{comment_id}/replies', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取评论的投票者
 * 获取评论的投票者
 */
export const getVoters = (params: GetVotersParams): Promise<UsersResponse> =>
  getRequest(
    buildURL('/comments/{comment_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type',
    ]),
  );

/**
 * 🔐把评论放入回收站
 * 仅管理员可调用该接口
 */
export const trash = (params: TrashParams): Promise<CommentResponse> =>
  postRequest(buildURL('/comments/{comment_id}/trash', params, ['include']));

/**
 * 🔐批量把评论放入回收站
 * 仅管理员可调用该接口。
 */
export const trashMultiple = (
  params: TrashMultipleParams,
): Promise<CommentsResponse> =>
  postRequest(buildURL('/comments/{comment_ids}/trash', params, ['include']));

/**
 * 🔐把评论移出回收站
 * 仅管理员可调用该接口。
 */
export const untrash = (params: UntrashParams): Promise<CommentResponse> =>
  postRequest(buildURL('/comments/{comment_id}/untrash', params, ['include']));

/**
 * 🔐批量把评论移出回收站
 * 仅管理员可调用该接口。
 */
export const untrashMultiple = (
  params: UntrashMultipleParams,
): Promise<CommentsResponse> =>
  postRequest(buildURL('/comments/{comment_ids}/untrash', params, ['include']));

/**
 * 修改评论
 * 管理员可修改评论。评论作者是否可修改评论，由管理员在后台的设置决定。
 */
export const update = (params: UpdateParams): Promise<CommentResponse> =>
  patchRequest(
    buildURL('/comments/{comment_id}', params, ['include']),
    buildRequestBody(params, ['content']),
  );
