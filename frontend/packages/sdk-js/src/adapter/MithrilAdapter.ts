import m from 'mithril';
import globalOptions from '../defaults';
import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from '../util/misc';
import BrowserAbstract from './abstract/Browser';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';

type requestFail = {
  message: string;
  code: number;
  response: any;
};

/**
 * 浏览器适配器，使用 mithril.js 的 request 方法实现
 */
export default class extends BrowserAbstract
  implements RequestAdapterInterface {
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    const headers: PlainObject = {};

    const token = this.getStorage('token');
    if (token) {
      headers.token = token;
    }

    if (options.data && options.data instanceof FormData) {
      headers['Content-Type'] = 'multipart/form-data';
    }

    return new Promise((resolve, reject): void => {
      m.request({
        method: options.method || 'GET',
        url: `${globalOptions.apiPath || ''}${options.url || ''}`,
        body: options.data,
        headers,
        background: true,
        config: xhr => {
          if (
            globalOptions.beforeSend &&
            globalOptions.beforeSend() === false
          ) {
            xhr.abort();
          }
        },
        // 默认空响应为 null，这里确保空响应返回 {}
        deserialize: (data): ResponseInterface => JSON.parse(data),
      })
        .then(data => (data.code === 0 ? resolve(data) : reject(data)))
        .catch((err: requestFail) =>
          reject({
            code: 999999,
            message: err.message,
          }),
        );
    });
  }
}
