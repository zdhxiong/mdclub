type EVENT_NAME =
  | 'user_update'
  | 'layout_update'
  | 'route_update'
  | 'login_open'
  | 'register_open'
  | 'reset_open'
  | 'users_dialog_open'
  | 'share_dialog_open'
  | 'report_dialog_open'
  | 'users_follow_updated'
  | 'topics_follow_updated'
  | 'questions_follow_updated'
  | 'articles_follow_updated'
  | 'question_updated'
  | 'notification_count_update'
  | 'comments_dialog_open';

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
