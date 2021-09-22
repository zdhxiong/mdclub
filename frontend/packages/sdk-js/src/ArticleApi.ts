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
  article_id: number;
}

interface AddFollowParams {
  article_id: number;
}

interface AddVoteParams {
  article_id: number;

  /**
   * 投票类型
   */
  type: VoteRequestBodyTypeEnum;
}

interface CreateParams {
  include?: Array<string>;

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

interface CreateCommentParams {
  article_id: number;
  include?: Array<string>;

  /**
   * 评论内容
   */
  content: string;
}

interface DeleteFollowParams {
  article_id: number;
}

interface DeleteMultipleParams {
  article_id?: Array<number>;
}

interface DeleteVoteParams {
  article_id: number;
}

interface DestroyParams {
  article_id: number;
}

interface DestroyMultipleParams {
  topic_id?: Array<number>;
}

interface GetParams {
  article_id: number;
}

interface GetCommentsParams {
  article_id: number;
  page?: number;
  per_page?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  per_page?: number;
  order?: string;
  article_id?: number;
  user_id?: number;
  topic_id?: number;
}

interface GetFollowersParams {
  article_id: number;
  page?: number;
  per_page?: number;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  per_page?: number;
  order?: string;
  include?: Array<string>;
  article_id?: number;
  user_id?: number;
  topic_id?: number;
}

interface GetVotersParams {
  article_id: number;
  page?: number;
  per_page?: number;
  include?: Array<string>;
  type?: 'up' | 'down';
}

interface RestoreParams {
  article_id: number;
}

interface RestoreMultipleParams {
  article_id?: Array<number>;
}

interface UpdateParams {
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
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    return del(buildURL(`${className}.del`, '/articles/{article_id}', params));
  },

  /**
   * 添加关注
   * @param params.article_id 文章ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    return post(
      buildURL(
        `${className}.addFollow`,
        '/articles/{article_id}/followers',
        params,
      ),
    );
  },

  /**
   * 为文章投票
   * @param params.article_id 文章ID
   * @param params.VoteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> => {
    return post(
      buildURL(`${className}.addVote`, '/articles/{article_id}/voters', params),
      buildRequestBody(params, ['type']),
    );
  },

  /**
   * 发表文章
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.ArticleRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  create: (params: CreateParams): Promise<ArticleResponse> => {
    return post(
      buildURL(`${className}.create`, '/articles', params, ['include']),
      buildRequestBody(params, [
        'title',
        'topic_id',
        'content_markdown',
        'content_rendered',
      ]),
    );
  },

  /**
   * 在指定文章下发表评论
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.article_id 文章ID
   * @param params.CommentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  createComment: (params: CreateCommentParams): Promise<CommentResponse> => {
    return post(
      buildURL(
        `${className}.createComment`,
        '/articles/{article_id}/comments',
        params,
        ['include'],
      ),
      buildRequestBody(params, ['content']),
    );
  },

  /**
   * 取消关注
   * @param params.article_id 文章ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    return del(
      buildURL(
        `${className}.deleteFollow`,
        '/articles/{article_id}/followers',
        params,
      ),
    );
  },

  /**
   * 🔐批量删除文章
   * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。  文章被删除后，进入回收站。管理员可在后台恢复文章。
   * @param params.article_id 用“,”分隔的文章ID，最多可提供100个ID
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.deleteMultiple`, '/articles', params, [
        'article_id',
      ]),
    );
  },

  /**
   * 取消为文章的投票
   * @param params.article_id 文章ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> => {
    return del(
      buildURL(
        `${className}.deleteVote`,
        '/articles/{article_id}/voters',
        params,
      ),
    );
  },

  /**
   * 🔐删除指定文章
   * 仅管理员可调用该接口。
   * @param params.article_id 文章ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.destroy`, '/trash/articles/{article_id}', params),
    );
  },

  /**
   * 🔐批量删除回收站中的话题
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 topic_id 参数，则将清空回收站中的所有文章。
   * @param params.topic_id 用“,”分隔的话题ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    return del(
      buildURL(`${className}.destroyMultiple`, '/trash/articles', params, [
        'topic_id',
      ]),
    );
  },

  /**
   * 获取指定文章信息
   * @param params.article_id 文章ID
   */
  get: (params: GetParams): Promise<ArticleResponse> => {
    return get(buildURL(`${className}.get`, '/articles/{article_id}', params));
  },

  /**
   * 获取指定文章的评论列表
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.article_id 文章ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> => {
    return get(
      buildURL(
        `${className}.getComments`,
        '/articles/{article_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      ),
    );
  },

  /**
   * 🔐获取回收站中的文章列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;，默认为 &#x60;-delete_time&#x60;
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.article_id 文章ID
   * @param params.user_id 用户ID
   * @param params.topic_id 话题ID
   */
  getDeleted: (params: GetDeletedParams): Promise<ArticlesResponse> => {
    return get(
      buildURL(`${className}.getDeleted`, '/trash/articles', params, [
        'page',
        'per_page',
        'order',
        'article_id',
        'user_id',
        'topic_id',
      ]),
    );
  },

  /**
   * 获取指定文章的关注者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.article_id 文章ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    return get(
      buildURL(
        `${className}.getFollowers`,
        '/articles/{article_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      ),
    );
  },

  /**
   * 获取文章列表
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.article_id 文章ID
   * @param params.user_id 用户ID
   * @param params.topic_id 话题ID
   */
  getList: (params: GetListParams): Promise<ArticlesResponse> => {
    return get(
      buildURL(`${className}.getList`, '/articles', params, [
        'page',
        'per_page',
        'order',
        'include',
        'article_id',
        'user_id',
        'topic_id',
      ]),
    );
  },

  /**
   * 获取文章的投票者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.article_id 文章ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> => {
    return get(
      buildURL(
        `${className}.getVoters`,
        '/articles/{article_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      ),
    );
  },

  /**
   * 🔐恢复指定文章
   * 仅管理员可调用该接口。
   * @param params.article_id 文章ID
   */
  restore: (params: RestoreParams): Promise<ArticleResponse> => {
    return post(
      buildURL(`${className}.restore`, '/trash/articles/{article_id}', params),
    );
  },

  /**
   * 🔐批量恢复文章
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.article_id 用“,”分隔的文章ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    return post(
      buildURL(`${className}.restoreMultiple`, '/trash/articles', params, [
        'article_id',
      ]),
    );
  },

  /**
   * 更新文章信息
   * 管理员可修改文章。文章作者是否可修改文章，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.article_id 文章ID
   * @param params.ArticleRequestBody
   */
  update: (params: UpdateParams): Promise<ArticleResponse> => {
    return patch(
      buildURL(`${className}.update`, '/articles/{article_id}', params),
      buildRequestBody(params, [
        'title',
        'topic_id',
        'content_markdown',
        'content_rendered',
      ]),
    );
  },
};
