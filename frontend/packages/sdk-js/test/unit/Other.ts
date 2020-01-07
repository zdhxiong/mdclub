import defaults from '../../es/defaults';
import errors from '../utils/errors';
import { failed, success } from '../utils/result';

describe('Other', () => {
  it('404 接口', () => {
    return defaults
      .adapter!.request({
        method: 'GET',
        url: '/404',
      })
      .catch(response => {
        response.code === errors.SYSTEM_API_NOT_FOUND
          ? success()
          : failed('404 接口 code 错误');
      });
  });
});
