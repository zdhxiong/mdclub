import { get, post, del } from './util/requestAlias';
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
   * 包含的关联数据，用“,”分隔。可以为 `reporter`, `question`, `answer`, `article`, `comment`, `user`
   */
  include?: Array<
    'reporter' | 'question' | 'answer' | 'article' | 'comment' | 'user'
  >;
  /**
   * 举报理由
   */
  reason: string;
}

interface DeleteMultipleParams {
  /**
   * 类型和ID之间用“:”分隔，多个记录之间用“,”分隔，最多可提供100个。例如 question:12,comment:34
   */
  target?: Array<string>;
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
   * 包含的关联数据，用“,”分隔。可以为 `question`, `answer`, `article`, `comment`, `user`
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
   * 包含的关联数据，用“,”分隔。可以为 `reporter`, `question`, `answer`, `article`, `comment`, `user`
   */
  include?: Array<
    'reporter' | 'question' | 'answer' | 'article' | 'comment' | 'user'
  >;
}

const className = 'ReportApi';

/**
 * ReportApi
 */
export default {
  /**
   * 🔐删除举报
   * 仅管理员可调用该接口
   * @param params.reportable_type 目标类型
   * @param params.reportable_id 目标ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> =>
    del(
      buildURL(
        `${className}.del`,
        '/reports/{reportable_type}/{reportable_id}',
        params,
      ),
    ),

  /**
   * 添加举报
   * 添加举报
   * @param params.reportable_type 目标类型
   * @param params.reportable_id 目标ID
   * @param params.ReportRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;reporter&#x60;, &#x60;question&#x60;, &#x60;answer&#x60;, &#x60;article&#x60;, &#x60;comment&#x60;, &#x60;user&#x60;
   */
  create: (params: CreateParams): Promise<ReportResponse> =>
    post(
      buildURL(
        `${className}.create`,
        '/reports/{reportable_type}/{reportable_id}',
        params,
        ['include'],
      ),
      buildRequestBody(params, ['reason']),
    ),

  /**
   * 🔐批量删除举报
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
   * @param params.target 类型和ID之间用“:”分隔，多个记录之间用“,”分隔，最多可提供100个。例如 question:12,comment:34
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> =>
    del(
      buildURL(`${className}.deleteMultiple`, '/reports', params, ['target']),
    ),

  /**
   * 🔐获取被举报的内容列表
   * 仅管理员可调用该接口
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;question&#x60;, &#x60;answer&#x60;, &#x60;article&#x60;, &#x60;comment&#x60;, &#x60;user&#x60;
   * @param params.reportable_type 目标类型
   */
  getList: (params: GetListParams): Promise<ReportGroupsResponse> =>
    get(
      buildURL(`${className}.getList`, '/reports', params, [
        'page',
        'per_page',
        'include',
        'reportable_type',
      ]),
    ),

  /**
   * 🔐获取被举报内容的举报详情
   * 仅管理员可调用该接口
   * @param params.reportable_type 目标类型
   * @param params.reportable_id 目标ID
   * @param params.page 当前页数
   * @param params.per_page 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。可以为 &#x60;reporter&#x60;, &#x60;question&#x60;, &#x60;answer&#x60;, &#x60;article&#x60;, &#x60;comment&#x60;, &#x60;user&#x60;
   */
  getReasons: (params: GetReasonsParams): Promise<ReportsResponse> =>
    get(
      buildURL(
        `${className}.getReasons`,
        '/reports/{reportable_type}/{reportable_id}',
        params,
        ['page', 'per_page', 'include'],
      ),
    ),
};
