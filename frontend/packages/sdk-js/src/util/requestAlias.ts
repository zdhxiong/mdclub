import defaults from '../defaults';
import { PlainObject } from './misc';
import { ResponseInterface } from '../adapter/AdapterInterface';
import { isUndefined } from 'mdui.jq/es/utils';

if (isUndefined(defaults.adapter)) {
  throw new Error(
    'adapter must be set. e.g. new Browser() or new MiniProgram()',
  );
}

const requestHandle = (
  method: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE',
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> =>
  defaults.adapter!.request({ method, url, data });

export const get = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle('GET', url, data);

export const post = (
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> => requestHandle('POST', url, data);

export const patch = (
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> => requestHandle('PATCH', url, data);

export const put = (
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> => requestHandle('PUT', url, data);

export const del = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle('DELETE', url, data);
