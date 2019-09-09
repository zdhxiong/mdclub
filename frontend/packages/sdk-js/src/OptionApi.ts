import defaults from './defaults';
import { get, post, put, patch, del } from './util/requestAlias';
import { urlParamReplace } from './util/url';
import { OptionResponse, Option } from './models';

interface GetParams {}

interface UpdateParams {
  option: Option;
}

/**
 * OptionApi
 */
export default {
  /**
   * 获取站点全局设置参数
   */
  get: (): Promise<OptionResponse> => {
    const url =
      defaults.apiPath + urlParamReplace('OptionApi.get', '/options', {}, []);

    return get(url);
  },

  /**
   * 🔐更新站点全局设置
   * 仅管理员可调用该接口
   * @param params.option
   */
  update: (params: UpdateParams): Promise<OptionResponse> => {
    const url =
      defaults.apiPath +
      urlParamReplace('OptionApi.update', '/options', params, []);

    return patch(url, params.option || {});
  },
};
