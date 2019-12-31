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
  UsersResponse,
  AnswersResponse,
  QuestionResponse,
  CommentsResponse,
  QuestionsResponse,
  VoteCountResponse,
  EmptyResponse,
  FollowerCountResponse,
  VoteRequestBodyTypeEnum,
} from './models';

interface DeleteParams {
  /**
   * 提问ID
   */
  question_id: number;
}

interface AddFollowParams {
  /**
   * 提问ID
   */
  question_id: number;
}

interface AddVoteParams {
  /**
   * 提问ID
   */
  question_id: number;
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

interface CreateAnswerParams {
  /**
   * 提问ID
   */
  question_id: number;
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

interface CreateCommentParams {
  /**
   * 提问ID
   */
  question_id: number;
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
   * 提问ID
   */
  question_id: number;
}

interface DeleteMultipleParams {
  /**
   * 多个用 `,` 分隔的提问ID，最多可提供 100 个 ID
   */
  question_ids: string;
}

interface DeleteVoteParams {
  /**
   * 提问ID
   */
  question_id: number;
}

interface GetParams {
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface GetAnswersParams {
  /**
   * 提问ID
   */
  question_id: number;
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
}

interface GetCommentsParams {
  /**
   * 提问ID
   */
  question_id: number;
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
   * 提问ID
   */
  question_id: number;
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
   * 提问ID
   */
  question_id?: number;
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
   * 提问ID
   */
  question_id: number;
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
   * 提问ID
   */
  question_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface TrashMultipleParams {
  /**
   * 多个用 `,` 分隔的提问ID，最多可提供 100 个 ID
   */
  question_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface UntrashParams {
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface UntrashMultipleParams {
  /**
   * 多个用 `,` 分隔的提问ID，最多可提供 100 个 ID
   */
  question_ids: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
}

interface UpdateParams {
  /**
   * 提问ID
   */
  question_id: number;
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

/**
 * 删除提问
 * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除提问。提问作者是否可删除提问，由管理员在后台的设置决定。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/questions/{question_id}', params));

/**
 * 添加关注
 * 添加关注
 */
export const addFollow = (
  params: AddFollowParams,
): Promise<FollowerCountResponse> =>
  postRequest(buildURL('/questions/{question_id}/followers', params));

/**
 * 为提问投票
 * 为提问投票
 */
export const addVote = (params: AddVoteParams): Promise<VoteCountResponse> =>
  postRequest(
    buildURL('/questions/{question_id}/voters', params),
    buildRequestBody(params, ['type']),
  );

/**
 * 发表提问
 * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
 */
export const create = (params: CreateParams): Promise<QuestionResponse> =>
  postRequest(
    buildURL('/questions', params, ['include']),
    buildRequestBody(params, [
      'title',
      'topic_id',
      'content_markdown',
      'content_rendered',
    ]),
  );

/**
 * 在指定提问下发表回答
 * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
 */
export const createAnswer = (
  params: CreateAnswerParams,
): Promise<AnswerResponse> =>
  postRequest(
    buildURL('/questions/{question_id}/answers', params, ['include']),
    buildRequestBody(params, ['content_markdown', 'content_rendered']),
  );

/**
 * 在指定提问下发表评论
 * 在指定提问下发表评论
 */
export const createComment = (
  params: CreateCommentParams,
): Promise<CommentResponse> =>
  postRequest(
    buildURL('/questions/{question_id}/comments', params, ['include']),
    buildRequestBody(params, ['content']),
  );

/**
 * 取消关注
 * 取消关注
 */
export const deleteFollow = (
  params: DeleteFollowParams,
): Promise<FollowerCountResponse> =>
  deleteRequest(buildURL('/questions/{question_id}/followers', params));

/**
 * 🔐批量删除提问
 * 仅管理员可调用该接口。 只要没有错误异常，无论是否有提问被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/questions/{question_ids}', params));

/**
 * 取消为提问的投票
 * 取消为提问的投票
 */
export const deleteVote = (
  params: DeleteVoteParams,
): Promise<VoteCountResponse> =>
  deleteRequest(buildURL('/questions/{question_id}/voters', params));

/**
 * 获取指定提问信息
 * 获取指定提问信息
 */
export const get = (params: GetParams): Promise<QuestionResponse> =>
  getRequest(buildURL('/questions/{question_id}', params, ['include']));

/**
 * 获取指定提问下的回答
 * 获取指定提问下的回答。
 */
export const getAnswers = (
  params: GetAnswersParams,
): Promise<AnswersResponse> =>
  getRequest(
    buildURL('/questions/{question_id}/answers', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定提问的评论
 * 获取指定提问的评论。
 */
export const getComments = (
  params: GetCommentsParams,
): Promise<CommentsResponse> =>
  getRequest(
    buildURL('/questions/{question_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include',
    ]),
  );

/**
 * 获取指定提问的关注者
 * 获取指定提问的关注者
 */
export const getFollowers = (
  params: GetFollowersParams,
): Promise<UsersResponse> =>
  getRequest(
    buildURL('/questions/{question_id}/followers', params, [
      'page',
      'per_page',
      'include',
    ]),
  );

/**
 * 获取提问列表
 * 获取提问列表。
 */
export const getList = (
  params: GetListParams = {},
): Promise<QuestionsResponse> =>
  getRequest(
    buildURL('/questions', params, [
      'page',
      'per_page',
      'order',
      'include',
      'question_id',
      'user_id',
      'topic_id',
      'trashed',
    ]),
  );

/**
 * 获取提问的投票者
 * 获取提问的投票者
 */
export const getVoters = (params: GetVotersParams): Promise<UsersResponse> =>
  getRequest(
    buildURL('/questions/{question_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type',
    ]),
  );

/**
 * 🔐把提问放入回收站
 * 仅管理员可调用该接口
 */
export const trash = (params: TrashParams): Promise<QuestionResponse> =>
  postRequest(buildURL('/questions/{question_id}/trash', params, ['include']));

/**
 * 🔐批量把提问放入回收站
 * 仅管理员可调用该接口。
 */
export const trashMultiple = (
  params: TrashMultipleParams,
): Promise<QuestionsResponse> =>
  postRequest(buildURL('/questions/{question_ids}/trash', params, ['include']));

/**
 * 🔐把提问移出回收站
 * 仅管理员可调用该接口。
 */
export const untrash = (params: UntrashParams): Promise<QuestionResponse> =>
  postRequest(
    buildURL('/questions/{question_id}/untrash', params, ['include']),
  );

/**
 * 🔐批量把提问移出回收站
 * 仅管理员可调用该接口。
 */
export const untrashMultiple = (
  params: UntrashMultipleParams,
): Promise<QuestionsResponse> =>
  postRequest(
    buildURL('/questions/{question_ids}/untrash', params, ['include']),
  );

/**
 * 更新提问信息
 * 管理员可修改提问。提问作者是否可修改提问，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
 */
export const update = (params: UpdateParams): Promise<QuestionResponse> =>
  patchRequest(
    buildURL('/questions/{question_id}', params, ['include']),
    buildRequestBody(params, [
      'title',
      'topic_id',
      'content_markdown',
      'content_rendered',
    ]),
  );
