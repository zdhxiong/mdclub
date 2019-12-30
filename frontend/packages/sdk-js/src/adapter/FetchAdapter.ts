import BrowserAbstract from "./abstract/Browser";
import globalOptions from '../defaults';
import { RequestAdapterInterface, RequestOptionsInterface, ResponseInterface } from '../util/misc';

export default class extends BrowserAbstract implements RequestAdapterInterface {
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    return new Promise((resolve, reject): void => {
      fetch(`${globalOptions.apiPath || ''}${options.url || ''}`, {
        method: options.method || 'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .then()
      .catch()
    });
  }
}
