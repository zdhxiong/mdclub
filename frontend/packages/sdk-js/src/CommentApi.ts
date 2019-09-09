import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  CommentResponse,
  VoteCountResponse,
  VoteRequestBody,
  UsersResponse,
  CommentRequestBody,
  EmptyResponse,
  CommentsResponse,
} from './models';

interface DeleteParams {
  commentId: number;
  force?: '1';
}

interface AddVoteParams {
  commentId: number;
  voteRequestBody: VoteRequestBody;
}

interface DeleteMultipleParams {
  commentId?: Array<number>;
  force?: '1';
}

interface DeleteVoteParams {
  commentId: number;
}

interface DestroyParams {
  commentId: number;
}

interface DestroyMultipleParams {
  commentId?: Array<number>;
}

interface GetParams {
  commentId: number;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
  commentId?: number;
  commentableId?: number;
  commentableType?: 'article' | 'question' | 'answer';
  userId?: number;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
  commentId?: number;
  commentableId?: number;
  commentableType?: 'article' | 'question' | 'answer';
  userId?: number;
}

interface GetVotersParams {
  commentId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
  type?: 'up' | 'down';
}

interface RestoreParams {
  commentId: number;
  include?: Array<string>;
}

interface RestoreMultipleParams {
  commentId?: Array<number>;
}

interface UpdateParams {
  commentId: number;
  commentRequestBody: CommentRequestBody;
  include?: Array<string>;
}

/**
 * CommentApi
 */
export default {
  /**
   * 删除评论
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除评论，默认为软删除，指定 force&#x3D;1 为硬删除。评论被删除后，进入回收站。管理员可在后台恢复评论。  评论作者是否可删除评论，由管理员在后台的设置决定。评论作者删除评论时只能硬删除，删除后不可恢复。
   * @param params.commentId 评论ID
   * @param params.force 🔐 若该参数为 1，则直接删除，不放入回收站。
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.del', '/comments/{comment_id}', params, [
        'force',
      ]);

    return del(url);
  },

  /**
   * 为评论投票
   * @param params.commentId 评论ID
   * @param params.voteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'CommentApi.addVote',
        '/comments/{comment_id}/voters',
        params,
        [],
      );

    return post(url, params.voteRequestBody || {});
  },

  /**
   * 🔐批量删除评论
   * 仅管理员可批量删除评论。  只要没有错误异常，无论是否有评论被删除，该接口都会返回成功。  评论被删除后，默认进入回收站。管理员可在后台恢复评论。
   * @param params.commentId 用“,”分隔的提问ID，最多可提供100个ID
   * @param params.force 🔐 若该参数为 1，则直接删除，不放入回收站。
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.deleteMultiple', '/comments', params, [
        'comment_id',
        'force',
      ]);

    return del(url);
  },

  /**
   * 取消为评论的投票
   * @param params.commentId 评论ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'CommentApi.deleteVote',
        '/comments/{comment_id}/voters',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐删除回收站中的指定评论
   * 仅管理员可调用该接口。
   * @param params.commentId 评论ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'CommentApi.destroy',
        '/trash/comments/{comment_id}',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除回收站中的评论
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.commentId 用“,”分隔的提问ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.destroyMultiple', '/trash/comments', params, [
        'comment_id',
      ]);

    return del(url);
  },

  /**
   * 获取评论详情
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.commentId 评论ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<CommentResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.get', '/comments/{comment_id}', params, [
        'include',
      ]);

    return get(url);
  },

  /**
   * 🔐获取回收站中的评论列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;delete_time&#x60;，默认为 &#x60;-delete_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.commentId 评论ID
   * @param params.commentableId 评论目标的ID
   * @param params.commentableType 评论目标类型
   * @param params.userId 用户ID
   */
  getDeleted: (params: GetDeletedParams): Promise<CommentsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.getDeleted', '/trash/comments', params, [
        'page',
        'per_page',
        'order',
        'include',
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
      ]);

    return get(url);
  },

  /**
   * 获取所有评论
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.commentId 评论ID
   * @param params.commentableId 评论目标的ID
   * @param params.commentableType 评论目标类型
   * @param params.userId 用户ID
   */
  getList: (params: GetListParams): Promise<CommentsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.getList', '/comments', params, [
        'page',
        'per_page',
        'order',
        'include',
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
      ]);

    return get(url);
  },

  /**
   * 获取评论的投票者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.commentId 评论ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'CommentApi.getVoters',
        '/comments/{comment_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      );

    return get(url);
  },

  /**
   * 🔐恢复指定评论
   * 仅管理员可调用该接口。  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.commentId 评论ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  restore: (params: RestoreParams): Promise<CommentResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'CommentApi.restore',
        '/trash/comments/{comment_id}',
        params,
        ['include'],
      );

    return post(url);
  },

  /**
   * 🔐批量恢复评论
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.commentId 用“,”分隔的提问ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.restoreMultiple', '/trash/comments', params, [
        'comment_id',
      ]);

    return post(url);
  },

  /**
   * 修改评论
   * 管理员可修改评论。评论作者是否可修改评论，由管理员在后台的设置决定。  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.commentId 评论ID
   * @param params.commentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  update: (params: UpdateParams): Promise<CommentResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('CommentApi.update', '/comments/{comment_id}', params, [
        'include',
      ]);

    return patch(url, params.commentRequestBody || {});
  },
};
