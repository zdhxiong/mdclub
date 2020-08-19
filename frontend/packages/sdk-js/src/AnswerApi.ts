import {
  getRequest,
  postRequest,
  patchRequest,
  deleteRequest,
} from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import {
  CommentResponse,
  AnswerResponse,
  VoteCountResponse,
  UsersResponse,
  AnswersResponse,
  EmptyResponse,
  CommentsResponse,
  VoteRequestBodyTypeEnum,
} from './models';

interface DeleteParams {
  /**
   * 回答ID
   */
  answer_id: number;
}

interface AddVoteParams {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * 投票类型
   */
  type: VoteRequestBodyTypeEnum;
}

interface CreateCommentParams {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `voting`
   */
  include?: Array<'user' | 'voting'>;
  /**
   * 评论内容
   */
  content: string;
}

interface DeleteMultipleParams {
  /**
   * 多个用 `,` 分隔的回答ID，最多可提供 100 个 ID
   */
  answer_ids: string;
}

interface DeleteVoteParams {
  /**
   * 回答ID
   */
  answer_id: number;
}

interface GetParams {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface GetCommentsParams {
  /**
   * 回答ID
   */
  answer_id: number;
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
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
  /**
   * 回答ID
   */
  answer_id?: number;
  /**
   * 提问ID
   */
  question_id?: number;
  /**
   * 用户ID
   */
  user_id?: number;
  /**
   * 🔐是否仅获取回收站中的数据
   */
  trashed?: boolean;
}

interface GetVotersParams {
  /**
   * 回答ID
   */
  answer_id: number;
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
   * 回答ID
   */
  answer_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface TrashMultipleParams {
  /**
   * 多个用 `,` 分隔的回答ID，最多可提供 100 个 ID
   */
  answer_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface UntrashParams {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface UntrashMultipleParams {
  /**
   * 多个用 `,` 分隔的回答ID，最多可提供 100 个 ID
   */
  answer_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

interface UpdateParams {
  /**
   * 回答ID
   */
  answer_id: number;
  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;
}

/**
 * 🔑删除回答
 *
 * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除回答。回答作者是否可删除回答，由管理员在后台的设置决定。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/answers/{answer_id}', params));

/**
 * 🔑为回答投票
 *
 * 为回答投票。
 */
export const addVote = (params: AddVoteParams): Promise<VoteCountResponse> =>
  postRequest(
    buildURL('/answers/{answer_id}/voters', params),
    buildRequestBody(params, ['type']),
  );

/**
 * 在指定回答下发表评论
 *
 * 在指定回答下发表评论。
 */
export const createComment = (
  params: CreateCommentParams,
): Promise<CommentResponse> =>
  postRequest(
    buildURL('/answers/{answer_id}/comments', params, ['include']),
    buildRequestBody(params, ['content']),
  );

/**
 * 🔐批量删除回答
 *
 * 批量删除回答。  只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/answers/{answer_ids}', params));

/**
 * 🔑取消为回答的投票
 *
 * 取消为回答的投票。
 */
export const deleteVote = (
  params: DeleteVoteParams,
): Promise<VoteCountResponse> =>
  deleteRequest(buildURL('/answers/{answer_id}/voters', params));

/**
 * 获取回答详情
 *
 * 获取回答详情。
 */
export const get = (params: GetParams): Promise<AnswerResponse> =>
  getRequest(buildURL('/answers/{answer_id}', params, ['include']));

/**
 * 获取指定回答的评论
 *
 * 获取指定回答的评论。
 */
export const getComments = (
  params: GetCommentsParams,
): Promise<CommentsResponse> =>
  getRequest(
    buildURL('/answers/{answer_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 🔐获取回答列表
 *
 * 获取回答列表。
 */
export const getList = (params: GetListParams = {}): Promise<AnswersResponse> =>
  getRequest(
    buildURL('/answers', params, [
      'page',
      'per_page',
      'order',
      'include',
      'answer_id',
      'question_id',
      'user_id',
      'trashed',
    ]),
  );

/**
 * 获取回答的投票者
 *
 * 获取回答的投票者。
 */
export const getVoters = (params: GetVotersParams): Promise<UsersResponse> =>
  getRequest(
    buildURL('/answers/{answer_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type',
    ]),
  );

/**
 * 🔐把回答放入回收站
 *
 * 把回答放入回收站。
 */
export const trash = (params: TrashParams): Promise<AnswerResponse> =>
  postRequest(buildURL('/answers/{answer_id}/trash', params, ['include']));

/**
 * 🔐批量把回答放入回收站
 *
 * 批量把回答放入回收站。
 */
export const trashMultiple = (
  params: TrashMultipleParams,
): Promise<AnswersResponse> =>
  postRequest(buildURL('/answers/{answer_ids}/trash', params, ['include']));

/**
 * 🔐把回答移出回收站
 *
 * 把回答移出回收站。
 */
export const untrash = (params: UntrashParams): Promise<AnswerResponse> =>
  postRequest(buildURL('/answers/{answer_id}/untrash', params, ['include']));

/**
 * 🔐批量把回答移出回收站
 *
 * 批量把回答移出回收站。
 */
export const untrashMultiple = (
  params: UntrashMultipleParams,
): Promise<AnswersResponse> =>
  postRequest(buildURL('/answers/{answer_ids}/untrash', params, ['include']));

/**
 * 🔑修改回答信息
 *
 * 管理员可修改回答。回答作者是否可修改回答，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
 */
export const update = (params: UpdateParams): Promise<AnswerResponse> =>
  patchRequest(
    buildURL('/answers/{answer_id}', params, ['include']),
    buildRequestBody(params, ['content_markdown', 'content_rendered']),
  );
