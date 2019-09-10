import { PlainObject } from './misc';
import param from 'mdui.jq/src/functions/param';
import defaults from '../defaults';
import { isNull, isUndefined } from 'mdui.jq/src/utils';

/**
 * 下划线转驼峰
 * @param str
 */
function underscoreToCamel(str: string): string {
  return str.replace(/_(\w)/g, (_, letter) => letter.toUpperCase());
}

/**
 * 替换 url 中的变量占位符，并添加 queryParam
 * @param name             当前调用的方法名称
 * @param path             含变量占位符的 url
 * @param params           含 path 参数、 query 参数、requestBody 参数的对象
 * @param queryParamNames  query 参数名数组
 */
export function buildURL(
  name: string,
  path: string,
  params: PlainObject,
  queryParamNames: string[] = [],
): string {
  // 替换 path 参数
  const url =
    defaults.apiPath +
    path.replace(/({.*?})/g, function(match): string {
      const pathParamName = underscoreToCamel(
        match.substr(1, match.length - 2),
      );

      if (isUndefined(params[pathParamName]) || isNull(params[pathParamName])) {
        throw new Error(
          `Missing required parameter ${pathParamName} when calling ${name}`,
        );
      }

      return String(params[pathParamName]);
    });

  // 添加 query 参数
  const queryObj: PlainObject<string> = {};
  queryParamNames.forEach(name => {
    if (!isUndefined(params[name]) && !isNull(params[name])) {
      queryObj[name] = String(params[underscoreToCamel(name)]);
    }
  });

  const queryString = param(queryObj);

  return queryString ? url + `?${queryString}` : url;
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

  requestBodyNames.forEach(name => {
    if (!isUndefined(params[name]) && !isNull(params[name])) {
      requestBody[name] = params;
    }
  });

  return requestBody;
}
