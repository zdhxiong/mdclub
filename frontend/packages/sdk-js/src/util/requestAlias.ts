import defaults from '../defaults';
import { ResponseInterface } from './misc';
import { isUndefined } from 'mdui.jq/es/utils';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';

if (isUndefined(defaults.adapter)) {
  throw new Error(
    'adapter must be set. e.g. new BrowserAdapter() or new MiniProgramAdapter()',
  );
}

const requestHandle = (
  method: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE',
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> =>
  defaults.adapter!.request({ method, url, data });

export const getRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle('GET', url, data);

export const postRequest = (
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> => requestHandle('POST', url, data);

export const patchRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle('PATCH', url, data);

export const putRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle('PUT', url, data);

export const deleteRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle('DELETE', url, data);
