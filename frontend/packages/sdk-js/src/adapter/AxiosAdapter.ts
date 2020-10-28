import axios from 'axios';
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
    return new Promise((resolve, reject): void => {
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

      if (globalOptions.beforeSend) {
        globalOptions.beforeSend();
      }

      axios({
        method: options.method || GET,
        url: `${globalOptions.apiPath}${options.url || ''}`,
        data: options.data,
        headers,
        timeout: globalOptions.timeout,
        responseType: 'json',
        validateStatus(status) {
          return status < 300 || status === 304;
        },
      })
        .then(({ data }) => {
          globalOptions.success && globalOptions.success(data);
          globalOptions.complete && globalOptions.complete();
          data.code === 0 ? resolve(data) : reject(data);
        })
        .catch(({ message }) => {
          globalOptions.error && globalOptions.error(message);
          globalOptions.complete && globalOptions.complete();
          reject({
            code: 999999,
            message,
          });
        });
    });
  }
}
