import { $document } from 'mdui/es/utils/dom';

/**
 * 订阅事件
 * @param name
 * @param fn
 */
export function subscribe(name, fn) {
  $document.on(name, (_, data) => {
    fn(data);
  });
}

/**
 * 触发事件
 * @param name
 * @param data
 */
export function emit(name, data = '') {
  $document.trigger(name, data);
}
