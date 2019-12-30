import axios from 'axios';
import BrowserAbstract from './abstract/Browser';
import globalOptions from '../defaults';
import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from '../util/misc';

export default class extends BrowserAbstract
  implements RequestAdapterInterface {
  constructor() {
    super();
  }

  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    return new Promise((resolve, reject): void => {
      axios({
        method: options.method || 'GET',
        url: `${globalOptions.apiPath || ''}${options.url || ''}`,
        params: options.method === 'GET' ? options.data : {},
        data: options.method !== 'GET' ? options.data : {},
        headers: {
          'Content-Type': 'application/json',
        },
      })
        .then(({ data }) => (data.code === 0 ? resolve(data) : reject(data)))
        .catch(({ message }) =>
          reject({
            code: 999999,
            message,
          }),
        );
    });
  }
}
