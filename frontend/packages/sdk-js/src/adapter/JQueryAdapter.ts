import $ from 'jquery';
import BrowserAbstract from './abstract/Browser';
import globalOptions from '../defaults';
import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from '../util/misc';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';

export default class extends BrowserAbstract
  implements RequestAdapterInterface {

  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    const headers: PlainObject = {
      'Content-Type': 'application/json',
      token: this.getStorage('token') || undefined,
    };

    if (options.data && options.data instanceof FormData) {
      headers['Content-Type'] = 'multipart/form-data';
    }

    return new Promise((resolve, reject): void => {
      $.ajax({
        method: options.method || 'GET',
        url: `${globalOptions.apiPath || ''}${options.url || ''}`,
        data: JSON.stringify(options.data),
        headers,
        dataType: 'json',
        contentType: 'application/json',
        global: false,
        beforeSend: () => {
          globalOptions.beforeSend && globalOptions.beforeSend()
        },
        success: data => {
          globalOptions.success && globalOptions.success(data);
          data.code === 0 ? resolve(data) : reject(data);
        },
        error: (_, textStatus) => {
          globalOptions.error && globalOptions.error(textStatus)
          reject({
            code: 999999,
            message: textStatus,
          })
        },
        complete: () => {
          globalOptions.complete && globalOptions.complete()
        },
      });
    });
  }
}
