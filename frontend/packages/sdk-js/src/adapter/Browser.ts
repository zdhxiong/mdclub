import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from './AdapterInterface';
import globalOptions from '../defaults';
import ajaxSetup from 'mdui.jq/es/functions/ajaxSetup';
import ajax from 'mdui.jq/es/functions/ajax';
import { isUndefined } from 'mdui.jq/es/utils';
import { PlainObject } from '../util/misc';

/**
 * 浏览器适配器
 */
export default class Browser implements RequestAdapterInterface {
  /**
   * 构造函数
   */
  constructor() {
    ajaxSetup({
      headers: {
        'Content-Type': 'application/json',
        token: this.getStorage('token') || undefined,
      },
      dataType: 'json',
      contentType: 'application/json',
      global: false,
      beforeSend: () => {
        globalOptions.beforeSend && globalOptions.beforeSend();
      },
      success: data => {
        globalOptions.success && globalOptions.success(data);
      },
      error: (xhr, textStatus) => {
        globalOptions.error && globalOptions.error(textStatus);
      },
      complete: () => {
        globalOptions.complete && globalOptions.complete();
      },
    });
  }

  /**
   * 获取数据存储
   * @param key
   */
  getStorage(key: string): string | null {
    return window.localStorage.getItem(key);
  }

  /**
   * 设置数据存储
   * @param key
   * @param data
   */
  setStorage(key: string, data: string): void {
    window.localStorage.setItem(key, data);
  }

  /**
   * 删除数据存储
   * @param key
   */
  removeStorage(key: string): void {
    window.localStorage.removeItem(key);
  }

  /**
   * 发送请求
   * @param options
   */
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    const headers: PlainObject = {};

    if (!isUndefined(options.data) && options.data instanceof FormData) {
      headers['Content-Type'] = 'multipart/form-data';
    }

    return new Promise((resolve, reject): void => {
      ajax({
        method: options.method || 'GET',
        url: (globalOptions.apiPath || '') + (options.url || ''),
        data: JSON.stringify(options.data),
        headers,
        success: data => {
          data.code === 0 ? resolve(data) : reject(data);
        },
        error: (xhr, textStatus) => {
          reject({
            code: 999999,
            message: textStatus,
          });
        },
      });
    });
  }
}
