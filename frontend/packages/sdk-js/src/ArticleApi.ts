import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  CommentResponse,
  VoteCountResponse,
  VoteRequestBody,
  ArticleResponse,
  CommentRequestBody,
  UsersResponse,
  EmptyResponse,
  FollowerCountResponse,
  ArticleRequestBody,
  ArticlesResponse,
  CommentsResponse,
} from './models';

interface DeleteParams {
  articleId: number;
}

interface AddFollowParams {
  articleId: number;
}

interface AddVoteParams {
  articleId: number;
  voteRequestBody: VoteRequestBody;
}

interface CreateParams {
  articleRequestBody: ArticleRequestBody;
  include?: Array<string>;
}

interface CreateCommentParams {
  articleId: number;
  commentRequestBody: CommentRequestBody;
  include?: Array<string>;
}

interface DeleteFollowParams {
  articleId: number;
}

interface DeleteMultipleParams {
  articleId?: Array<number>;
}

interface DeleteVoteParams {
  articleId: number;
}

interface DestroyParams {
  articleId: number;
}

interface DestroyMultipleParams {
  topicId?: Array<number>;
}

interface GetParams {
  articleId: number;
}

interface GetCommentsParams {
  articleId: number;
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
}

interface GetDeletedParams {
  page?: number;
  perPage?: number;
  order?: string;
  articleId?: number;
  userId?: number;
  topicId?: number;
}

interface GetFollowersParams {
  articleId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  order?: string;
  include?: Array<string>;
  articleId?: number;
  userId?: number;
  topicId?: number;
}

interface GetVotersParams {
  articleId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
  type?: 'up' | 'down';
}

interface RestoreParams {
  articleId: number;
}

interface RestoreMultipleParams {
  articleId?: Array<number>;
}

interface UpdateParams {
  articleId: number;
  articleRequestBody: ArticleRequestBody;
}

/**
 * ArticleApi
 */
export default {
  /**
   * 删除指定文章
   * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。  文章被删除后，进入回收站。管理员可在后台恢复文章。
   * @param params.articleId 文章ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.del', '/articles/{article_id}', params, []);

    return del(url);
  },

  /**
   * 添加关注
   * @param params.articleId 文章ID
   */
  addFollow: (params: AddFollowParams): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.addFollow',
        '/articles/{article_id}/followers',
        params,
        [],
      );

    return post(url);
  },

  /**
   * 为文章投票
   * @param params.articleId 文章ID
   * @param params.voteRequestBody
   */
  addVote: (params: AddVoteParams): Promise<VoteCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.addVote',
        '/articles/{article_id}/voters',
        params,
        [],
      );

    return post(url, params.voteRequestBody || {});
  },

  /**
   * 发表文章
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.articleRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  create: (params: CreateParams): Promise<ArticleResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.create', '/articles', params, ['include']);

    return post(url, params.articleRequestBody || {});
  },

  /**
   * 在指定文章下发表评论
   * &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.articleId 文章ID
   * @param params.commentRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  createComment: (params: CreateCommentParams): Promise<CommentResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.createComment',
        '/articles/{article_id}/comments',
        params,
        ['include'],
      );

    return post(url, params.commentRequestBody || {});
  },

  /**
   * 取消关注
   * @param params.articleId 文章ID
   */
  deleteFollow: (
    params: DeleteFollowParams,
  ): Promise<FollowerCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.deleteFollow',
        '/articles/{article_id}/followers',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除文章
   * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。  文章被删除后，进入回收站。管理员可在后台恢复文章。
   * @param params.articleId 用“,”分隔的文章ID，最多可提供100个ID
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.deleteMultiple', '/articles', params, [
        'article_id',
      ]);

    return del(url);
  },

  /**
   * 取消为文章的投票
   * @param params.articleId 文章ID
   */
  deleteVote: (params: DeleteVoteParams): Promise<VoteCountResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.deleteVote',
        '/articles/{article_id}/voters',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐删除指定文章
   * 仅管理员可调用该接口。
   * @param params.articleId 文章ID
   */
  destroy: (params: DestroyParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.destroy',
        '/trash/articles/{article_id}',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 🔐批量删除回收站中的话题
   * 仅管理员可调用该接口  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。  若没有提供 topic_id 参数，则将清空回收站中的所有文章。
   * @param params.topicId 用“,”分隔的话题ID，最多可提供100个ID
   */
  destroyMultiple: (params: DestroyMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.destroyMultiple', '/trash/articles', params, [
        'topic_id',
      ]);

    return del(url);
  },

  /**
   * 获取指定文章信息
   * @param params.articleId 文章ID
   */
  get: (params: GetParams): Promise<ArticleResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.get', '/articles/{article_id}', params, []);

    return get(url);
  },

  /**
   * 获取指定文章的评论列表
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;，默认为 &#x60;create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;voting&#x60;
   * @param params.articleId 文章ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getComments: (params: GetCommentsParams): Promise<CommentsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.getComments',
        '/articles/{article_id}/comments',
        params,
        ['page', 'per_page', 'order', 'include'],
      );

    return get(url);
  },

  /**
   * 🔐获取回收站中的文章列表
   * 仅管理员可调用该接口。  可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;、&#x60;delete_time&#x60;，默认为 &#x60;-delete_time&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.articleId 文章ID
   * @param params.userId 用户ID
   * @param params.topicId 话题ID
   */
  getDeleted: (params: GetDeletedParams): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.getDeleted', '/trash/articles', params, [
        'page',
        'per_page',
        'order',
        'article_id',
        'user_id',
        'topic_id',
      ]);

    return get(url);
  },

  /**
   * 获取指定文章的关注者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.articleId 文章ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getFollowers: (params: GetFollowersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.getFollowers',
        '/articles/{article_id}/followers',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },

  /**
   * 获取文章列表
   * 可排序字段包括 &#x60;vote_count&#x60;、&#x60;create_time&#x60;、&#x60;update_time&#x60;，默认为 &#x60;-create_time&#x60;  &#x60;include&#x60; 参数取值包括：&#x60;user&#x60;、&#x60;topics&#x60;、&#x60;is_following&#x60;、&#x60;voting&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.order 排序方式。在字段前加 &#x60;-&#x60; 表示倒序排列。例如 &#x60;create_time&#x60; 表示按时间顺序排列，&#x60;-create_time&#x60; 则表示按时间倒序排列。
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.articleId 文章ID
   * @param params.userId 用户ID
   * @param params.topicId 话题ID
   */
  getList: (params: GetListParams): Promise<ArticlesResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.getList', '/articles', params, [
        'page',
        'per_page',
        'order',
        'include',
        'article_id',
        'user_id',
        'topic_id',
      ]);

    return get(url);
  },

  /**
   * 获取文章的投票者
   * &#x60;include&#x60; 参数取值包括：&#x60;is_followed&#x60;、&#x60;is_following&#x60;、&#x60;is_me&#x60;
   * @param params.articleId 文章ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.type 默认获取全部投票类型的用户 &#x60;up&#x60; 表示仅获取投赞成票的用户 &#x60;down&#x60; 表示仅获取投反对票的用户
   */
  getVoters: (params: GetVotersParams): Promise<UsersResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.getVoters',
        '/articles/{article_id}/voters',
        params,
        ['page', 'per_page', 'include', 'type'],
      );

    return get(url);
  },

  /**
   * 🔐恢复指定文章
   * 仅管理员可调用该接口。
   * @param params.articleId 文章ID
   */
  restore: (params: RestoreParams): Promise<ArticleResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.restore',
        '/trash/articles/{article_id}',
        params,
        [],
      );

    return post(url);
  },

  /**
   * 🔐批量恢复文章
   * 仅管理员可调用该接口。  只要没有异常错误，无论是否有用户被禁用，该接口都会返回成功。
   * @param params.articleId 用“,”分隔的文章ID，最多可提供100个ID
   */
  restoreMultiple: (params: RestoreMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ArticleApi.restoreMultiple', '/trash/articles', params, [
        'article_id',
      ]);

    return post(url);
  },

  /**
   * 更新文章信息
   * 管理员可修改文章。文章作者是否可修改文章，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   * @param params.articleId 文章ID
   * @param params.articleRequestBody
   */
  update: (params: UpdateParams): Promise<ArticleResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ArticleApi.update',
        '/articles/{article_id}',
        params,
        [],
      );

    return patch(url, params.articleRequestBody || {});
  },
};
