type EVENT_NAME =
  | 'layout_update'
  | 'route_update'
  | 'searchbar_state_update'
  | 'appbar_state_update'
  | 'pagination_state_update'
  | 'datatable_update_row'
  | 'datatable_state_update'
  | 'topic_open'
  | 'topic_edit_open'
  | 'user_open'
  | 'user_edit_open'
  | 'question_open'
  | 'question_edit_open'
  | 'answer_open'
  | 'answer_edit_open'
  | 'article_open'
  | 'article_edit_open'
  | 'comment_open'
  | 'comment_edit_open'
  | 'reporters_open';

/**
 * 订阅事件
 * @param name
 * @param fn
 */
export declare function subscribe(name: EVENT_NAME, fn: (data: any) => void): void;

/**
 * 触发事件
 * @param name
 * @param data
 */
export declare function emit(name: EVENT_NAME, data?: any): void;
