import { getRequest, postRequest, deleteRequest } from './util/requestAlias';
import { buildURL, buildRequestBody } from './util/requestHandler';
import {
  ReportsResponse,
  ReportResponse,
  ReportGroupsResponse,
  EmptyResponse,
} from './models';

interface DeleteParams {
  /**
   * 目标类型
   */
  reportable_type: 'question' | 'answer' | 'article' | 'comment' | 'user';
  /**
   * 目标ID
   */
  reportable_id: number;
}

interface CreateParams {
  /**
   * 目标类型
   */
  reportable_type: 'question' | 'answer' | 'article' | 'comment' | 'user';
  /**
   * 目标ID
   */
  reportable_id: number;
  /**
   * 举报理由
   */
  reason: string;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `reporter`, `question`, `answer`, `article`, `comment`, `user`
   */
  include?: Array<
    'reporter' | 'question' | 'answer' | 'article' | 'comment' | 'user'
  >;
}

interface DeleteMultipleParams {
  /**
   * 类型和ID之间用 `:` 分隔，多个记录之间用 `,` 分隔，最多可提供 100 个。  例如 `question:12,comment:34`
   */
  report_targets: string;
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
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `question`, `answer`, `article`, `comment`, `user`
   */
  include?: Array<'question' | 'answer' | 'article' | 'comment' | 'user'>;
  /**
   * 目标类型
   */
  reportable_type?: 'question' | 'answer' | 'article' | 'comment' | 'user';
}

interface GetReasonsParams {
  /**
   * 目标类型
   */
  reportable_type: 'question' | 'answer' | 'article' | 'comment' | 'user';
  /**
   * 目标ID
   */
  reportable_id: number;
  /**
   * 当前页数
   */
  page?: number;
  /**
   * 每页条数（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用“,”分隔。可以为 `reporter`, `question`, `answer`, `article`, `comment`, `user`
   */
  include?: Array<
    'reporter' | 'question' | 'answer' | 'article' | 'comment' | 'user'
  >;
}

/**
 * 🔐删除举报
 *
 * 删除举报。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/reports/{reportable_type}:{reportable_id}', params));

/**
 * 🔑添加举报
 *
 * 添加举报。
 */
export const create = (params: CreateParams): Promise<ReportResponse> =>
  postRequest(
    buildURL('/reports/{reportable_type}:{reportable_id}', params, ['include']),
    buildRequestBody(params, ['reason']),
  );

/**
 * 🔐批量删除举报
 *
 * 批量删除举报。  只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/reports/{report_targets}', params));

/**
 * 🔐获取被举报的内容列表
 *
 * 获取被举报的内容列表。
 */
export const getList = (
  params: GetListParams = {},
): Promise<ReportGroupsResponse> =>
  getRequest(
    buildURL('/reports', params, [
      'page',
      'per_page',
      'include',
      'reportable_type',
    ]),
  );

/**
 * 🔐获取被举报内容的举报详情
 *
 * 获取被举报内容的举报详情。
 */
export const getReasons = (
  params: GetReasonsParams,
): Promise<ReportsResponse> =>
  getRequest(
    buildURL('/reports/{reportable_type}:{reportable_id}', params, [
      'page',
      'per_page',
      'include',
    ]),
  );
