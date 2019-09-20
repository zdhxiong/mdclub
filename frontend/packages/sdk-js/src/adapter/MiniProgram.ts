import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from './AdapterInterface';
import globalOptions from '../defaults';
import { isUndefined } from 'mdui.jq/es/utils';

/**
 * 小程序适配器
 *
 * 支持 微信小程序、支付宝小程序、钉钉小程序、百度小程序
 */
export default class MiniProgram implements RequestAdapterInterface {
  /**
   * 获取数据存储
   * @param key
   */
  getStorage(key: string): string | null {
    return wx.getStorageSync(key);
  }

  /**
   * 设置数据存储
   * @param key
   * @param data
   */
  setStorage(key: string, data: string): void {
    wx.setStorageSync(key, data);
  }

  /**
   * 删除数据存储
   * @param key
   */
  removeStorage(key: string): void {
    wx.removeStorageSync(key);
  }

  /**
   * 发送请求
   * @param options
   */
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    return new Promise((resolve, reject): void => {
      // 设置 header
      const header: wx.RequestHeader = {
        'Content-Type': 'application/json',
      };

      const token = this.getStorage('token');
      if (token) {
        header.token = token;
      }

      // 小程序不支持 patch 请求
      if (options.method === 'PATCH') {
        options.method = 'POST';
        header['X-Http-Method-Override'] = 'PATCH';
      }

      // beforeSend 返回 false 时，取消请求
      if (globalOptions.beforeSend) {
        if (globalOptions.beforeSend() === false) {
          reject('cancel');
          return;
        }
      }

      // request 或 uploadFile 方法的参数
      const url = (globalOptions.apiPath || '') + (options.url || '');
      const data = options.data;

      const success = (res: wx.DataResponse): void => {
        globalOptions.success && globalOptions.success(res.data);
        resolve(res.data as ResponseInterface);
      };

      const fail = (res: any): void => {
        globalOptions.error && globalOptions.error(res);
        reject(res);
      };

      const complete = (): void => {
        globalOptions.complete && globalOptions.complete();
      };

      if (!isUndefined(options.data) && options.data instanceof FormData) {
        wx.uploadFile({
          url,
          filePath: '',
          name: '',
          formData: data,
        });
      } else {
        wx.request({
          method: options.method || 'GET',
          url,
          data,
          header,
          success,
          fail,
          complete,
        });
      }
    });
  }
}
