import { getRequest } from './util/requestAlias';
import { buildURL } from './util/requestHandler';
import { StatsResponse } from './models';

interface GetParams {
  /**
   * 统计数据中包含的数据，用“,”分隔，可以为 `system_info`, `total_user`, `total_question`, `total_article`, `total_answer`, `total_comment`, `new_user`, `new_question`, `new_article`, `new_answer`, `new_comment`
   */
  include?: Array<
    | 'system_info'
    | 'total_user'
    | 'total_question'
    | 'total_article'
    | 'total_answer'
    | 'total_comment'
    | 'new_user'
    | 'new_question'
    | 'new_article'
    | 'new_answer'
    | 'new_comment'
  >;
  /**
   * 统计数据的起始日期，例如 `2017-3-14`
   */
  startDate?: string;
  /**
   * 统计数据的截止日期，例如 `2020-5-12`
   */
  endDate?: string;
}

/**
 * 🔐获取站点统计数据
 * 获取站点统计数据。
 */
export const get = (params: GetParams): Promise<StatsResponse> =>
  getRequest(buildURL('/stats', params, ['include', 'start_date', 'end_date']));
