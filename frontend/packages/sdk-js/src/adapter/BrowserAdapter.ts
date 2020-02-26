import ajax from 'mdui.jq/es/functions/ajax';
import extend from 'mdui.jq/es/functions/extend';
import globalOptions from '../defaults';
import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from '../util/misc';
import { GET } from '../util/requestMethod';
import BrowserAbstract from './abstract/Browser';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';

/**
 * 浏览器适配器，使用 mdui.jq 中的 ajax 函数实现
 */
export default class extends BrowserAbstract
  implements RequestAdapterInterface {
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    const isFormData = options.data instanceof FormData;
    let headers: PlainObject = {
      token: this.getStorage('token') || undefined,
    };

    if (options.headers) {
      headers = extend({}, headers, options.headers);
    }

    return new Promise((resolve, reject): void => {
      ajax({
        method: options.method || GET,
        url: `${globalOptions.apiPath}${options.url || ''}`,
        data: isFormData ? options.data : JSON.stringify(options.data),
        headers,
        dataType: 'json',
        contentType: isFormData ? false : 'application/json',
        timeout: globalOptions.timeout,
        global: false,
        beforeSend: () => {
          globalOptions.beforeSend && globalOptions.beforeSend();
        },
        success: data => {
          globalOptions.success && globalOptions.success(data);
          data.code === 0 ? resolve(data) : reject(data);
        },
        error: (_, textStatus) => {
          globalOptions.error && globalOptions.error(textStatus);
          reject({
            code: 999999,
            message: textStatus,
          });
        },
        complete: () => {
          globalOptions.complete && globalOptions.complete();
        },
      });
    });
  }
}
