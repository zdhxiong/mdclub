import defaults from '../defaults';
import { ResponseInterface } from './misc';
import { isUndefined } from 'mdui.jq/es/utils';
import PlainObject from 'mdui.jq/es/interfaces/PlainObject';
import { METHOD_TYPE, GET, POST, PUT, PATCH, DELETE } from './requestMethod';

if (isUndefined(defaults.adapter)) {
  throw new Error(
    'adapter must be set. e.g. new BrowserAdapter() or new MiniProgramAdapter()',
  );
}

const requestHandle = (
  method: METHOD_TYPE,
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> => {
  const headers: PlainObject<string> = {};
  const XHttpMethodOverride = 'X-Http-Method-Override';

  if (defaults.methodOverride) {
    if (method === PATCH || method === PUT) {
      headers[XHttpMethodOverride] = method;
      method = POST;
    }

    if (method === DELETE) {
      headers[XHttpMethodOverride] = method;
      method = GET;
    }
  }

  // header 中添加 accept
  const accepts = ['application/json'];
  if (
    typeof document !== 'undefined' &&
    !![].map &&
    document
      .createElement('canvas')
      .toDataURL('image/webp')
      .indexOf('data:image/webp') === 0
  ) {
    accepts.push('image/webp');
  }
  headers['Accept'] = accepts.join(', ');

  return defaults.adapter!.request({ method, url, data, headers });
};

export const getRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle(GET, url, data);

export const postRequest = (
  url: string,
  data?: PlainObject | FormData,
): Promise<ResponseInterface> => requestHandle(POST, url, data);

export const patchRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle(PATCH, url, data);

export const putRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle(PUT, url, data);

export const deleteRequest = (
  url: string,
  data?: PlainObject,
): Promise<ResponseInterface> => requestHandle(DELETE, url, data);
