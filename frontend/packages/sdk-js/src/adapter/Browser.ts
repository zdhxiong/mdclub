import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from './AdapterInterface';
import globalOptions from '../defaults';
import ajaxSetup from 'mdui.jq/src/functions/ajaxSetup';
import ajax from 'mdui.jq/src/functions/ajax';
import { isUndefined } from "../../types/mdui.jq/src/utils";
import { PlainObject } from "../util/misc";

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
   * 发送请求
   * @param options
   */
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    const headers: PlainObject = {};

    if (!isUndefined(options.data) && options.data instanceof FormData) {
      headers['Content-Type'] = 'multipart/form-data';
    }

    return ajax({
      method: options.method || 'GET',
      url: (globalOptions.apiPath || '') + (options.url || ''),
      data: options.data,
      headers,
    });
  }
}
