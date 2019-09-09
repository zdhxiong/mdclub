import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import {
  ReportsResponse,
  ReportResponse,
  ReportRequestBody,
  ReportGroupsResponse,
  EmptyResponse,
} from './models';

interface DeleteParams {
  reportableType: 'question' | 'answer' | 'article' | 'comment' | 'user';
  reportableId: number;
}

interface CreateParams {
  reportableType: 'question' | 'answer' | 'article' | 'comment' | 'user';
  reportableId: number;
  reportRequestBody: ReportRequestBody;
  include?: Array<string>;
}

interface DeleteMultipleParams {
  target?: Array<string>;
}

interface GetListParams {
  page?: number;
  perPage?: number;
  include?: Array<string>;
  reportableType?: 'question' | 'answer' | 'article' | 'comment' | 'user';
}

interface GetReasonsParams {
  reportableType: 'question' | 'answer' | 'article' | 'comment' | 'user';
  reportableId: number;
  page?: number;
  perPage?: number;
  include?: Array<string>;
}

/**
 * ReportApi
 */
export default {
  /**
   * 🔐删除举报
   * 仅管理员可调用该接口
   * @param params.reportableType 目标类型
   * @param params.reportableId 目标ID
   */
  del: (params: DeleteParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ReportApi.del',
        '/reports/{reportable_type}/{reportable_id}',
        params,
        [],
      );

    return del(url);
  },

  /**
   * 添加举报
   * &#x60;include&#x60; 参数取值包括：&#x60;reporter&#x60;、&#x60;question&#x60;、&#x60;answer&#x60;、&#x60;article&#x60;、&#x60;comment&#x60;、&#x60;user&#x60;
   * @param params.reportableType 目标类型
   * @param params.reportableId 目标ID
   * @param params.reportRequestBody
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  create: (params: CreateParams): Promise<ReportResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ReportApi.create',
        '/reports/{reportable_type}/{reportable_id}',
        params,
        ['include'],
      );

    return post(url, params.reportRequestBody || {});
  },

  /**
   * 🔐批量删除举报
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
   * @param params.target 类型和ID之间用“:”分隔，多个记录之间用“,”分隔，最多可提供100个。例如 question:12,comment:34
   */
  deleteMultiple: (params: DeleteMultipleParams): Promise<EmptyResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ReportApi.deleteMultiple', '/reports', params, [
        'target',
      ]);

    return del(url);
  },

  /**
   * 🔐获取被举报的内容列表
   * 仅管理员可调用该接口  &#x60;include&#x60; 参数取值包括：&#x60;question&#x60;、&#x60;answer&#x60;、&#x60;article&#x60;、&#x60;comment&#x60;、&#x60;user&#x60;
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   * @param params.reportableType 目标类型
   */
  getList: (params: GetListParams): Promise<ReportGroupsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('ReportApi.getList', '/reports', params, [
        'page',
        'per_page',
        'include',
        'reportable_type',
      ]);

    return get(url);
  },

  /**
   * 🔐获取被举报内容的举报详情
   * 仅管理员可调用该接口  &#x60;include&#x60; 参数取值包括：&#x60;reporter&#x60;、&#x60;question&#x60;、&#x60;answer&#x60;、&#x60;article&#x60;、&#x60;comment&#x60;、&#x60;user&#x60;
   * @param params.reportableType 目标类型
   * @param params.reportableId 目标ID
   * @param params.page 当前页数
   * @param params.perPage 每页条数（最大为 100）
   * @param params.include 包含的关联数据，用“,”分隔。
   */
  getReasons: (params: GetReasonsParams): Promise<ReportsResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace(
        'ReportApi.getReasons',
        '/reports/{reportable_type}/{reportable_id}',
        params,
        ['page', 'per_page', 'include'],
      );

    return get(url);
  },
};
