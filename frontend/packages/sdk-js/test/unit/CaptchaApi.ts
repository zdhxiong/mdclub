import * as CaptchaApi from '../../es/CaptchaApi';
import models from '../utils/models';
import { removeDefaultToken } from '../utils/token';
import { matchModel } from '../utils/validator';

describe('CaptchaApi', () => {
  it('生成新验证码', () => {
    removeDefaultToken();

    return CaptchaApi.generate().then(response => {
      matchModel(response.data, models.Captcha);
    });
  });
});
