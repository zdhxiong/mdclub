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

export default class extends BrowserAbstract
  implements RequestAdapterInterface {
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    return new Promise((resolve, reject): void => {
      let headers: PlainObject = {
        Accept: 'application/json, text/plain, */*',
        'Content-Type': 'application/json',
        token: this.getStorage('token') || undefined,
      };

      if (options.data && options.data instanceof FormData) {
        headers['Content-Type'] = 'multipart/form-data';
      }

      if (options.headers) {
        headers = extend({}, headers, options.headers);
      }

      if (globalOptions.beforeSend) {
        globalOptions.beforeSend();
      }

      fetch(`${globalOptions.apiPath}${options.url || ''}`, {
        method: options.method || GET,
        headers,
        body: JSON.stringify(options.data),
        mode: 'cors',
      })
        .then((response) => response.json())
        .then((data) => {
          globalOptions.success && globalOptions.success(data);
          globalOptions.complete && globalOptions.complete();
          data.code === 0 ? resolve(data) : reject(data);
        })
        .catch((err) => {
          globalOptions.error && globalOptions.error(err);
          globalOptions.complete && globalOptions.complete();
          reject({
            code: 999999,
            err,
          });
        });
    });
  }
}
