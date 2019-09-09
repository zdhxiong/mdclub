import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  AnswerRequestBody,
  CommentResponse,
  AnswerResponse,
  VoteRequestBody,
  CommentRequestBody,
  UsersResponse,
  AnswersResponse,
  QuestionResponse,
  CommentsResponse,
  QuestionsResponse,
  VoteCountResponse,
  QuestionRequestBody,
  EmptyResponse,
  FollowerCountResponse,
} from './models';

interface DeleteParams {
  questionId: number;
}

interface AddFollowParams {
  questionId: number;
}

interface AddVoteParams {
  questionId: number;
  voteRequestBody: VoteRequestBody;
}

interface CreateParams {
  questionRequestBody: QuestionRequestBody;
}

interface CreateAnswerParams {
  questionId: number;
  answerRequestBody: AnswerRequestBody;
  include?: Array<string>;
}

interface CreateCommentParams {
  questionId: number;
  commentRequestBody: CommentRequestBody;
  include?: Array<string>;
}

interface DeleteFollowParams {
  questionId: number;
}

interface DeleteMultipleParams {
  questionId?: Array<number>;
}

interface DeleteVoteParams {
  questionId: number;
}

interface DestroyParams {
  questionId: number;
}

interface DestroyMultipleParams {
  questionId?: Array<number>;
}

interface GetParams {
  questionId: number;
  include?: Array<string>;
}

interface GetAnswersParams {
  questionId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetCommentsParams {
  questionId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  perPage?: number;
  order?: string;
  questionId?: number;
  userId?: number;
  topicId?: number;
}

interface GetFollowersParams {
  questionId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
  questionId?: number;
  userId?: number;
  topicId?: number;
}

interface GetVotersParams {
  questionId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
  type?: 'up' | 'down';
}

interface RestoreParams {
  questionId: number;
}

interface RestoreMultipleParams {
  questionId?: Array<number>;
}

interface UpdateParams {
  questionId: number;
  questionRequestBody: QuestionRequestBody;
}

/**
 * QuestionApi
 */
export default {
  /**
   * 删除指定提问
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除提问。提问作者是否可删除提问，由管理员在后台的设置决定。  提问被删除后，进入回收站。管理员可在后台恢复提问。
   * @param params.questionId 提问ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.del',
        '/questions/{question_id}',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 添加关注
   * @param params.questionId 提问ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.addFollow',
        '/questions/{question_id}/followers',
        params,
        [],
      );

    return post(url);
  },

  /**
   * 为提问投票
   * @param params.questionId 提问ID
   * @param params.voteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.addVote',
        '/questions/{question_id}/voters',
        params,
        [],
      );

    return post(url, params.voteRequestBody || {});
  },

  /**
   * 发表提问
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.questionRequestBody
   */
  create: (params: CreateParams): Promise<QuestionResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('QuestionApi.create', '/questions', params, []);

    return post(url, params.questionRequestBody || {});
  },

  /**
   * 在指定提问下发表回答
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.questionId 提问ID
   * @param params.answerRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  createAnswer: (params: CreateAnswerParams): Promise<AnswerResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.createAnswer',
        '/questions/{question_id}/answers',
        params,
        ['include'],
      );

    return post(url, params.answerRequestBody || {});
  },

  /**
   * 在指定提问下发表评论
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.questionId 提问ID
   * @param params.commentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  createComment: (params: CreateCommentParams): Promise<CommentResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.createComment',
        '/questions/{question_id}/comments',
        params,
        ['include'],
      );

    return post(url, params.commentRequestBody || {});
  },

  /**
   * 取消关注
   * @param params.questionId 提问ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.deleteFollow',
        '/questions/{question_id}/followers',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除提问
   * 只要没有错误异常，无论是否有提问被删除，该接口都会返回成功。  管理员可删除提问。提问作者是否可删除提问，由管理员在后台的设置决定。  提问被删除后，进入回收站。管理员可在后台恢复提问。
   * @param params.questionId 用“,”分隔的提问ID，最多可提供100个ID
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('QuestionApi.deleteMultiple', '/questions', params, [
        'question_id',
      ]);

    return del(url);
  },

  /**
   * 取消为提问的投票
   * @param params.questionId 提问ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.deleteVote',
        '/questions/{question_id}/voters',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐删除指定提问
   * 仅管理员可调用该接口。
   * @param params.questionId 提问ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.destroy',
        '/trash/questions/{question_id}',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除回收站中的提问
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 question_id 参数，则将清空回收站中的所有提问。
   * @param params.questionId 用“,”分隔的提问ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.destroyMultiple',
        '/trash/questions',
        params,
        ['question_id'],
      );

    return del(url);
  },

  /**
   * 获取指定提问信息
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.questionId 提问ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<QuestionResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('QuestionApi.get', '/questions/{question_id}', params, [
        'include',
      ]);

    return get(url);
  },

  /**
   * 获取指定提问下的回答
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.questionId 提问ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getAnswers: (params: GetAnswersParams): Promise<AnswersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.getAnswers',
        '/questions/{question_id}/answers',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 获取指定提问的评论
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.questionId 提问ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.getComments',
        '/questions/{question_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 🔐获取回收站中的提问列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;，默认为 &#x60;-delete_time&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.questionId 提问ID
   * @param params.userId 用户ID
   * @param params.topicId 话题ID
   */
  getDeleted: (params: GetDeletedParams): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('QuestionApi.getDeleted', '/trash/questions', params, [
        'page',
        'per_page',
        'order',
        'question_id',
        'user_id',
        'topic_id',
      ]);

    return get(url);
  },

  /**
   * 获取指定提问的关注者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.questionId 提问ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.getFollowers',
        '/questions/{question_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取提问列表
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-update_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.questionId 提问ID
   * @param params.userId 用户ID
   * @param params.topicId 话题ID
   */
  getList: (params: GetListParams): Promise<QuestionsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('QuestionApi.getList', '/questions', params, [
        'page',
        'per_page',
        'order',
        'include',
        'question_id',
        'user_id',
        'topic_id',
      ]);

    return get(url);
  },

  /**
   * 获取提问的投票者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.questionId 提问ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.getVoters',
        '/questions/{question_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      );

    return get(url);
  },

  /**
   * 🔐恢复指定提问
   * 仅管理员可调用该接口。
   * @param params.questionId 提问ID
   */
  restore: (params: RestoreParams): Promise<QuestionResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.restore',
        '/trash/questions/{question_id}',
        params,
        [],
      );

    return post(url);
  },

  /**
   * 🔐批量恢复提问
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.questionId 用“,”分隔的提问ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.restoreMultiple',
        '/trash/questions',
        params,
        ['question_id'],
      );

    return post(url);
  },

  /**
   * 更新提问信息
   * 管理员可修改提问。提问作者是否可修改提问，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.questionId 提问ID
   * @param params.questionRequestBody
   */
  update: (params: UpdateParams): Promise<QuestionResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'QuestionApi.update',
        '/questions/{question_id}',
        params,
        [],
      );

    return patch(url, params.questionRequestBody || {});
  },
};
