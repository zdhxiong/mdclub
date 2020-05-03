import PlainObject from 'mdui.jq/es/interfaces/PlainObject';
import param from 'mdui.jq/es/functions/param';

/**
 * 替换 url 中的变量占位符，并添加 queryParam
 * @param path             含变量占位符的 url
 * @param params           含 path 参数、 query 参数、requestBody 参数的对象
 * @param queryParamNames  query 参数名数组
 */
export function buildURL(
  path: string,
  params: PlainObject = {},
  queryParamNames: string[] = [],
): string {
  // 替换 path 参数
  const url = path.replace(/({.*?})/g, (match): string => {
    const pathParamName = match.substr(1, match.length - 2);

    if (params[pathParamName] == null) {
      throw new Error(`Missing required parameter ${pathParamName}`);
    }

    return String(params[pathParamName]);
  });

  // 添加 query 参数
  const queryObj: PlainObject<string> = {};
  queryParamNames.forEach((name) => {
    if (params[name] != null) {
      queryObj[name] = String(params[name]);
    }
  });

  const queryString = param(queryObj);

  return queryString ? `${url}?${queryString}` : url;
}

/**
 * 生成 requestBody 参数
 * @param params           含 path 参数、 query 参数、requestBody 参数的对象
 * @param requestBodyNames requestBody 参数名数组
 */
export function buildRequestBody(
  params: PlainObject,
  requestBodyNames: string[],
): PlainObject {
  const requestBody: PlainObject = {};

  requestBodyNames.forEach((name) => {
    if (params[name] != null) {
      requestBody[name] = params[name];
    }
  });

  return requestBody;
}
