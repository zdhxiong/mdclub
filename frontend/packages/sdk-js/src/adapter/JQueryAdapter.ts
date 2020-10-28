import $ from 'jquery';
import BrowserAbstract from './abstract/Browser';
import globalOptions from '../defaults';
import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from '../util/misc';
import { GET } from '../util/requestMethod';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';
import extend from 'mdui.jq/es/functions/extend';

export default class
  extends BrowserAbstract
  implements RequestAdapterInterface {
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    let headers: PlainObject = {
      'Content-Type': 'application/json',
      token: this.getStorage('token') || undefined,
    };

    if (options.data && options.data instanceof FormData) {
      headers['Content-Type'] = 'multipart/form-data';
    }

    if (options.headers) {
      headers = extend({}, headers, options.headers);
    }

    return new Promise((resolve, reject): void => {
      $.ajax({
        method: options.method || GET,
        url: `${globalOptions.apiPath}${options.url || ''}`,
        data: JSON.stringify(options.data),
        headers,
        dataType: 'json',
        contentType: 'application/json',
        timeout: globalOptions.timeout,
        global: false,
        beforeSend: () => {
          globalOptions.beforeSend && globalOptions.beforeSend();
        },
        success: (data) => {
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
