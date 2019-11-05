import { get, post, patch, del } from './util/requestAlias';
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
   * 包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
   */
  include?: Array<'user' | 'topics' | 'is_following' | 'voting'>;
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
}

interface CreateAnswerParams {
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
   */
  include?: Array<'user' | 'question' | 'voting'>;

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
   * 提问ID
   */
  question_id: number;
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
   * 提问ID
   */
  question_id: number;
}

interface DeleteMultipleParams {
  /**
   * 用“,”分隔的提问ID，最多可提供100个ID
   */
  question_id?: Array<number>;
}

interface DeleteVoteParams {
  /**
   * 提问ID
   */
  question_id: number;
}

interface DestroyParams {
  /**
   * 提问ID
   */
  question_id: number;
}

interface DestroyMultipleParams {
  /**
   * 用“,”分隔的提问ID，最多可提供100个ID
   */
  question_id?: Array<number>;
}

interface GetParams {
  /**
   * 提问ID
   */
  question_id: number;
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `topics`, `is_following`, `voting`
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
   * 排序方式。在字段前加 `-` 表示倒序排列。  可排序字段包括 `vote_count`、`create_time`、`update_time`。默认为 `-create_time`。
   */
  order?:
    | 'vote_count'
    | 'create_time'
    | 'update_time'
    | '-vote_count'
    | '-create_time'
    | '-update_time';
  /**
   * 包含的关联数据，用“,”分隔。可以为 `user`, `question`, `voting`
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
   * 提问ID
   */
  question_id: number;
}

interface RestoreMultipleParams {
  /**
   * 用“,”分隔的提问ID，最多可提供100个ID
   */
  question_id?: Array<number>;
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
}

const className = 'QuestionApi';

/**
 * QuestionApi
 */
export default {
  /**
   * 删除指定提问
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除提问。提问作者是否可删除提问，由管理员在后台的设置决定。  提问被删除后，进入回收站。管理员可在后台恢复提问。
   * @param params.question_id 提问ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> =>
    del(buildURL(`${className}.del`, '/questions/{question_id}', params)),

  /**
   * 添加关注
   * 添加关注
   * @param params.question_id 提问ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> =>
    post(
      buildURL(
        `${className}.addFollow`,
        '/questions/{question_id}/followers',
        params,
      ),
    ),

  /**
   * 为提问投票
   * 为提问投票
   * @param params.question_id 提问ID
   * @param params.VoteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> =>
    post(
      buildURL(
        `${className}.addVote`,
        '/questions/{question_id}/voters',
        params,
      ),
      buildRequestBody(params, ['type']),
    ),

  /**
   * 发表提问
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.QuestionCreateRequestBody
   */
  create: (params: CreateParams): Promise<QuestionResponse> =>
    post(
      buildURL(`${className}.create`, '/questions', params),
      buildRequestBody(params, [
        'title',
        'topic_id',
        'content_markdown',
        'content_rendered',
      ]),
    ),

  /**
   * 在指定提问下发表回答
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.question_id 提问ID
   * @param params.AnswerRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   */
  createAnswer: (params: CreateAnswerParams): Promise<AnswerResponse> =>
    post(
      buildURL(
        `${className}.createAnswer`,
        '/questions/{question_id}/answers',
        params,
        ['include'],
      ),
      buildRequestBody(params, ['content_markdown', 'content_rendered']),
    ),

  /**
   * 在指定提问下发表评论
   * 在指定提问下发表评论
   * @param params.question_id 提问ID
   * @param params.CommentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  createComment: (params: CreateCommentParams): Promise<CommentResponse> =>
    post(
      buildURL(
        `${className}.createComment`,
        '/questions/{question_id}/comments',
        params,
        ['include'],
      ),
      buildRequestBody(params, ['content']),
    ),

  /**
   * 取消关注
   * 取消关注
   * @param params.question_id 提问ID
   */
  deleteFollow: (params: DeleteFollowParams): Promise<FollowerCountResponse> =>
    del(
      buildURL(
        `${className}.deleteFollow`,
        '/questions/{question_id}/followers',
        params,
      ),
    ),

  /**
   * 🔐批量删除提问
   * 只要没有错误异常，无论是否有提问被删除，该接口都会返回成功。  管理员可删除提问。提问作者是否可删除提问，由管理员在后台的设置决定。  提问被删除后，进入回收站。管理员可在后台恢复提问。
   * @param params.question_id 用“,”分隔的提问ID，最多可提供100个ID
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.deleteMultiple`, '/questions', params, [
        'question_id',
      ]),
    ),

  /**
   * 取消为提问的投票
   * 取消为提问的投票
   * @param params.question_id 提问ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> =>
    del(
      buildURL(
        `${className}.deleteVote`,
        '/questions/{question_id}/voters',
        params,
      ),
    ),

  /**
   * 🔐删除指定提问
   * 仅管理员可调用该接口。
   * @param params.question_id 提问ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> =>
    del(
      buildURL(
        `${className}.destroy`,
        '/trash/questions/{question_id}',
        params,
      ),
    ),

  /**
   * 🔐批量删除回收站中的提问
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 question_id 参数，则将清空回收站中的所有提问。
   * @param params.question_id 用“,”分隔的提问ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.destroyMultiple`, '/trash/questions', params, [
        'question_id',
      ]),
    ),

  /**
   * 获取指定提问信息
   * 获取指定提问信息
   * @param params.question_id 提问ID
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   */
  get: (params: GetParams): Promise<QuestionResponse> =>
    get(
      buildURL(`${className}.get`, '/questions/{question_id}', params, [
        'include',
      ]),
    ),

  /**
   * 获取指定提问下的回答
   * 获取指定提问下的回答。
   * @param params.question_id 提问ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;。
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   */
  getAnswers: (params: GetAnswersParams): Promise<AnswersResponse> =>
    get(
      buildURL(
        `${className}.getAnswers`,
        '/questions/{question_id}/answers',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    ),

  /**
   * 获取指定提问的评论
   * 获取指定提问的评论。
   * @param params.question_id 提问ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;voting&#x60;
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> =>
    get(
      buildURL(
        `${className}.getComments`,
        '/questions/{question_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    ),

  /**
   * 🔐获取回收站中的提问列表
   * 仅管理员可调用该接口。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;。默认为 &#x60;-delete_time&#x60;
   * @param params.question_id 提问ID
   * @param params.user_id 用户ID
   * @param params.topic_id 话题ID
   */
  getDeleted: (params: GetDeletedParams): Promise<QuestionsResponse> =>
    get(
      buildURL(`${className}.getDeleted`, '/trash/questions', params, [
        'page',
        'per_page',
        'order',
        'question_id',
        'user_id',
        'topic_id',
      ]),
    ),

  /**
   * 获取指定提问的关注者
   * 获取指定提问的关注者
   * @param params.question_id 提问ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> =>
    get(
      buildURL(
        `${className}.getFollowers`,
        '/questions/{question_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      ),
    ),

  /**
   * 获取提问列表
   * 获取提问列表。
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;。默认为 &#x60;-create_time&#x60;
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;user&#x60;, &#x60;topics&#x60;, &#x60;is_following&#x60;, &#x60;voting&#x60;
   * @param params.question_id 提问ID
   * @param params.user_id 用户ID
   * @param params.topic_id 话题ID
   */
  getList: (params: GetListParams): Promise<QuestionsResponse> =>
    get(
      buildURL(`${className}.getList`, '/questions', params, [
        'page',
        'per_page',
        'order',
        'include',
        'question_id',
        'user_id',
        'topic_id',
      ]),
    ),

  /**
   * 获取提问的投票者
   * 获取提问的投票者
   * @param params.question_id 提问ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;is_followed&#x60;, &#x60;is_following&#x60;, &#x60;is_me&#x60;
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> =>
    get(
      buildURL(
        `${className}.getVoters`,
        '/questions/{question_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      ),
    ),

  /**
   * 🔐恢复指定提问
   * 仅管理员可调用该接口。
   * @param params.question_id 提问ID
   */
  restore: (params: RestoreParams): Promise<QuestionResponse> =>
    post(
      buildURL(
        `${className}.restore`,
        '/trash/questions/{question_id}',
        params,
      ),
    ),

  /**
   * 🔐批量恢复提问
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.question_id 用“,”分隔的提问ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> =>
    post(
      buildURL(`${className}.restoreMultiple`, '/trash/questions', params, [
        'question_id',
      ]),
    ),

  /**
   * 更新提问信息
   * 管理员可修改提问。提问作者是否可修改提问，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.question_id 提问ID
   * @param params.QuestionUpdateRequestBody
   */
  update: (params: UpdateParams): Promise<QuestionResponse> =>
    patch(
      buildURL(`${className}.update`, '/questions/{question_id}', params),
      buildRequestBody(params, [
        'title',
        'topic_id',
        'content_markdown',
        'content_rendered',
      ]),
    ),
};
