import { getRequest, postRequest, deleteRequest } from './util/requestAlias';
import { buildURL } from './util/requestHandler';
import {
  NotificationsResponse,
  NotificationResponse,
  EmptyResponse,
  NotificationCountResponse,
} from './models';

interface DeleteParams {
  /**
   * 通知ID
   */
  notification_id: number;
}

interface DeleteAllParams {
  /**
   * 通知类型
   */
  type?:
    | 'question_answered'
    | 'question_commented'
    | 'question_deleted'
    | 'article_commented'
    | 'article_deleted'
    | 'answer_commented'
    | 'answer_deleted'
    | 'comment_replied'
    | 'comment_deleted';
}

interface DeleteMultipleParams {
  /**
   * 多个用 `,` 分隔的通知ID，最多提供 100 个 ID
   */
  notification_ids: string;
}

interface GetCountParams {
  /**
   * 通知类型
   */
  type?:
    | 'question_answered'
    | 'question_commented'
    | 'question_deleted'
    | 'article_commented'
    | 'article_deleted'
    | 'answer_commented'
    | 'answer_deleted'
    | 'comment_replied'
    | 'comment_deleted';
}

interface GetListParams {
  /**
   * 当前页数，默认为 1
   */
  page?: number;
  /**
   * 每页条数，默认为 15（最大为 100）
   */
  per_page?: number;
  /**
   * 响应中需要包含的关联数据，用 `,` 分隔。可以为 `receiver`, `sender`, `article`, `question`, `answer`, `comment`, `reply`
   */
  include?: Array<
    | 'receiver'
    | 'sender'
    | 'article'
    | 'question'
    | 'answer'
    | 'comment'
    | 'reply'
  >;
  /**
   * 通知类型
   */
  type?:
    | 'question_answered'
    | 'question_commented'
    | 'question_deleted'
    | 'article_commented'
    | 'article_deleted'
    | 'answer_commented'
    | 'answer_deleted'
    | 'comment_replied'
    | 'comment_deleted';
  /**
   * 默认包含已读和未读的通知。若 `read` 为 `true`，则仅包含已读的通知；若为 `false`，则仅包含未读的通知。
   */
  read?: boolean;
}

interface ReadParams {
  /**
   * 通知ID
   */
  notification_id: number;
  /**
   * 响应中需要包含的关联数据，用 `,` 分隔。可以为 `receiver`, `sender`, `article`, `question`, `answer`, `comment`, `reply`
   */
  include?: Array<
    | 'receiver'
    | 'sender'
    | 'article'
    | 'question'
    | 'answer'
    | 'comment'
    | 'reply'
  >;
}

interface ReadAllParams {
  /**
   * 通知类型
   */
  type?:
    | 'question_answered'
    | 'question_commented'
    | 'question_deleted'
    | 'article_commented'
    | 'article_deleted'
    | 'answer_commented'
    | 'answer_deleted'
    | 'comment_replied'
    | 'comment_deleted';
}

interface ReadMultipleParams {
  /**
   * 多个用 `,` 分隔的通知ID，最多提供 100 个 ID
   */
  notification_ids: string;
  /**
   * 响应中需要包含的关联数据，用 `,` 分隔。可以为 `receiver`, `sender`, `article`, `question`, `answer`, `comment`, `reply`
   */
  include?: Array<
    | 'receiver'
    | 'sender'
    | 'article'
    | 'question'
    | 'answer'
    | 'comment'
    | 'reply'
  >;
}

/**
 * 🔑删除一条通知
 *
 * 只要没有错误异常，无论是否有通知被删除，该接口都会返回成功。
 */
export const del = (params: DeleteParams): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/notifications/{notification_id}', params));

/**
 * 🔑删除所有通知
 *
 * 只要没有错误异常，无论是否有通知被删除，该接口都会返回成功。
 */
export const deleteAll = (
  params: DeleteAllParams = {},
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/notifications', params, ['type']));

/**
 * 🔑批量删除通知
 *
 * 只要没有错误异常，无论是否有通知被删除，该接口都会返回成功。
 */
export const deleteMultiple = (
  params: DeleteMultipleParams,
): Promise<EmptyResponse> =>
  deleteRequest(buildURL('/notifications/{notification_ids}', params));

/**
 * 🔑获取未读通知数量
 *
 * 获取未读通知数量。
 */
export const getCount = (
  params: GetCountParams = {},
): Promise<NotificationCountResponse> =>
  getRequest(buildURL('/notifications/count', params, ['type']));

/**
 * 🔑获取通知列表
 *
 * 获取通知列表。
 */
export const getList = (
  params: GetListParams = {},
): Promise<NotificationsResponse> =>
  getRequest(
    buildURL('/notifications', params, [
      'page',
      'per_page',
      'include',
      'type',
      'read',
    ]),
  );

/**
 * 🔑把一条通知标记为已读
 *
 * 把一条通知标记为已读。
 */
export const read = (params: ReadParams): Promise<NotificationResponse> =>
  postRequest(
    buildURL('/notifications/{notification_id}/read', params, ['include']),
  );

/**
 * 🔑把所有通知标记为已读
 *
 * 只要没有错误异常。无论是否有通知被标记为已读，该接口都会返回成功。
 */
export const readAll = (params: ReadAllParams = {}): Promise<EmptyResponse> =>
  postRequest(buildURL('/notifications/read', params, ['type']));

/**
 * 🔑批量把通知标记为已读
 *
 * 批量把通知标记为已读。
 */
export const readMultiple = (
  params: ReadMultipleParams,
): Promise<NotificationsResponse> =>
  postRequest(
    buildURL('/notifications/{notification_ids}/read', params, ['include']),
  );
