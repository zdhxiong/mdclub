import { PlainObject } from './misc';
import param from 'mdui.jq/src/functions/param';

/**
 * 下划线转驼峰
 * @param str
 */
function underscoreToCamel(str: string): string {
  return str.replace(/_(\w)/g, (_, letter) => letter.toUpperCase());
}

/**
 * 替换 url 中的变量占位符，并添加 queryParam
 * @param name            当前调用的方法名称
 * @param path            含变量占位符的 url
 * @param params          含 path 参数和 query 参数的对象
 * @param queryParamNames query 参数名数组
 */
export function urlParamReplace(
  name: string,
  path: string,
  params: PlainObject,
  queryParamNames: string[],
): string {
  path = path.replace(/({.*?})/g, function(match): string {
    const pathParamName = underscoreToCamel(match.substr(1, match.length - 2));

    if (params[pathParamName] === undefined || params[pathParamName] === null) {
      throw new Error(
        `Missing required parameter ${pathParamName} when calling ${name}`,
      );
    }

    return String(params[pathParamName]);
  });

  const queryObj: { [name: string]: string } = {};
  queryParamNames.forEach(name => {
    if (params[name] !== undefined && params[name] !== null) {
      queryObj[name] = String(params[underscoreToCamel(name)]);
    }
  });

  const queryString = param(queryObj);

  return queryString ? path + `?${queryString}` : path;
}
