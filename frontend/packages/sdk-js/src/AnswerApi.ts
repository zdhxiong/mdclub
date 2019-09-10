import { get, post, patch, del } from './util/requestAlias';
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
  answer_id: number;
}

interface AddVoteParams {
  answer_id: number;

  /**
   * 投票类型
   */
  type: VoteRequestBodyTypeEnum;
}

interface CreateCommentParams {
  answer_id: number;
  include?: Array<string>;

  /**
   * 评论内容
   */
  content: string;
}

interface DeleteMultipleParams {
  answer_id?: Array<number>;
}

interface DeleteVoteParams {
  answer_id: number;
}

interface DestroyParams {
  answer_id: number;
}

interface DestroyMultipleParams {
  answer_id?: Array<number>;
}

interface GetParams {
  answer_id: number;
  include?: Array<string>;
}

interface GetCommentsParams {
  answer_id: number;
  page?: number;
  per_page?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  per_page?: number;
  order?: string;
  answer_id?: number;
  question_id?: number;
  user_id?: number;
}

interface GetListParams {
  page?: number;
  per_page?: number;
  order?: string;
  include?: Array<string>;
  answer_id?: number;
  question_id?: number;
  user_id?: number;
}

interface GetVotersParams {
  answer_id: number;
  page?: number;
  per_page?: number;
  include?: Array<string>;
  type?: 'up' | 'down';
}

interface RestoreParams {
  answer_id: number;
}

interface RestoreMultipleParams {
  answer_id?: Array<number>;
}

interface UpdateParams {
  answer_id: number;
  include?: Array<string>;

  /**
   * Markdown 格式的正文
   */
  content_markdown?: string;
  /**
   * HTML 格式的正文
   */
  content_rendered?: string;
}

const className = 'AnswerApi';

/**
 * AnswerApi
 */
export default {
  /**
   * 删除指定回答
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除回答。回答作者是否可删除回答，由管理员在后台的设置决定。  回答被删除后，进入回收站。管理员可在后台恢复回答。
   * @param params.answer_id 回答ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    return del(buildURL(`${className}.del`, '/answers/{answer_id}', params));
  },

  /**
   * 为回答投票
   * @param params.answer_id 回答ID
   * @param params.VoteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> => {
    return post(
      buildURL(`${className}.addVote`, '/answers/{answer_id}/voters', params),
      buildRequestBody(params, ['type']),
    );
  },

  /**
   * 在指定回答下发表评论
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.answer_id 回答ID
   * @param params.CommentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  createComment: (params: CreateCommentParams): Promise<CommentResponse> => {
    return post(
      buildURL(
        `${className}.createComment`,
        '/answers/{answer_id}/comments',
        params,
        ['include'],
      ),
      buildRequestBody(params, ['content']),
    );
  },

  /**
   * 🔐批量删除回答
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除回答。回答作者是否可删除回答，由管理员在后台的设置决定。  回答被删除后，进入回收站。管理员可在后台恢复回答。
   * @param params.answer_id 用“,”分隔的回答ID，最多可提供100个ID
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.deleteMultiple`, '/answers', params, [
        'answer_id',
      ]),
    );
  },

  /**
   * 取消为回答的投票
   * @param params.answer_id 回答ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> => {
    return del(
      buildURL(
        `${className}.deleteVote`,
        '/answers/{answer_id}/voters',
        params,
      ),
    );
  },

  /**
   * 🔐删除指定回答
   * 仅管理员可调用该接口。
   * @param params.answer_id 回答ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.destroy`, '/trash/answers/{answer_id}', params),
    );
  },

  /**
   * 🔐批量删除回收站中的回答
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 answer_id 参数，则将清空回收站中的所有回答。
   * @param params.answer_id 用“,”分隔的回答ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.destroyMultiple`, '/trash/answers', params, [
        'answer_id',
      ]),
    );
  },

  /**
   * 获取回答详情
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.answer_id 回答ID
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  get: (params: GetParams): Promise<AnswerResponse> => {
    return get(
      buildURL(`${className}.get`, '/answers/{answer_id}', params, ['include']),
    );
  },

  /**
   * 获取指定回答的评论
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.answer_id 回答ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> => {
    return get(
      buildURL(
        `${className}.getComments`,
        '/answers/{answer_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 🔐获取回收站中的回答列表
   * 仅管理员可调用该接口。 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;，默认为 &#x60;-delete_time&#x60;
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.answer_id 回答ID
   * @param params.question_id 提问ID
   * @param params.user_id 用户ID
   */
  getDeleted: (params: GetDeletedParams): Promise<AnswersResponse> => {
    return get(
      buildURL(`${className}.getDeleted`, '/trash/answers', params, [
        'page',
        'per_page',
        'order',
        'answer_id',
        'question_id',
        'user_id',
      ]),
    );
  },

  /**
   * 获取回答列表
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.answer_id 回答ID
   * @param params.question_id 提问ID
   * @param params.user_id 用户ID
   */
  getList: (params: GetListParams): Promise<AnswersResponse> => {
    return get(
      buildURL(`${className}.getList`, '/answers', params, [
        'page',
        'per_page',
        'order',
        'include',
        'answer_id',
        'question_id',
        'user_id',
      ]),
    );
  },

  /**
   * 获取回答的投票者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.answer_id 回答ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> => {
    return get(
      buildURL(
        `${className}.getVoters`,
        '/answers/{answer_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      ),
    );
  },

  /**
   * 🔐恢复指定回答
   * 仅管理员可调用该接口。
   * @param params.answer_id 回答ID
   */
  restore: (params: RestoreParams): Promise<AnswerResponse> => {
    return post(
      buildURL(`${className}.restore`, '/trash/answers/{answer_id}', params),
    );
  },

  /**
   * 🔐批量恢复回答
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.answer_id 用“,”分隔的回答ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    return post(
      buildURL(`${className}.restoreMultiple`, '/trash/answers', params, [
        'answer_id',
      ]),
    );
  },

  /**
   * 修改回答信息
   * 管理员可修改回答。回答作者是否可修改回答，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;, &#x60;question&#x60;, &#x60;voting&#x60;
   * @param params.answer_id 回答ID
   * @param params.AnswerRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  update: (params: UpdateParams): Promise<AnswerResponse> => {
    return patch(
      buildURL(`${className}.update`, '/answers/{answer_id}', params, [
        'include',
      ]),
      buildRequestBody(params, ['content_markdown', 'content_rendered']),
    );
  },
};
