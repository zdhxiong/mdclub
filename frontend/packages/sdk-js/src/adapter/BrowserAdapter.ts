import ajax from 'mdui.jq/es/functions/ajax';
import ajaxSetup from 'mdui.jq/es/functions/ajaxSetup';
import globalOptions from '../defaults';
import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from '../util/misc';
import BrowserAbstract from './abstract/Browser';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';

/**
 * 浏览器适配器，使用 mdui.jq 中的 ajax 函数实现
 */
export default class extends BrowserAbstract
  implements RequestAdapterInterface {
  constructor() {
    super();

    ajaxSetup({
      headers: {
        'Content-Type': 'application/json',
        token: this.getStorage('token') || undefined,
      },
      dataType: 'json',
      contentType: 'application/json',
      global: false,
      beforeSend: () => globalOptions.beforeSend && globalOptions.beforeSend(),
      success: data => globalOptions.success && globalOptions.success(data),
      error: (_, textStatus) =>
        globalOptions.error && globalOptions.error(textStatus),
      complete: () => globalOptions.complete && globalOptions.complete(),
    });
  }

  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    const headers: PlainObject = {};

    if (options.data && options.data instanceof FormData) {
      headers['Content-Type'] = 'multipart/form-data';
    }

    return new Promise((resolve, reject): void => {
      ajax({
        method: options.method || 'GET',
        url: `${globalOptions.apiPath || ''}${options.url || ''}`,
        data: JSON.stringify(options.data),
        headers,
        success: data => (data.code === 0 ? resolve(data) : reject(data)),
        error: (_, textStatus) =>
          reject({
            code: 999999,
            message: textStatus,
          }),
      });
    });
  }
}
