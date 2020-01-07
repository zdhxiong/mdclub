import * as TokenApi from '../es/TokenApi';
import models from './utils/models';
import {
  removeDefaultToken,
  removeManagerToken,
  removeNormalToken,
  setManagerToken,
  setNormalToken,
} from './utils/token';
import { matchModel } from './utils/validator';
import { failed } from './utils/result';

// 首先分别用管理员账号和普通账号登录，获取对应的 token，供后续测试使用
before(() => {
  const managerLogin = TokenApi.login({
    name: 'zdhxiong',
    password: '123456',
  }).then(response => {
    matchModel(response.data, models.Token);
    setManagerToken(response.data.token);
  });

  const normalLogin = TokenApi.login({
    name: '1131699723',
    password: '123456',
  }).then(response => {
    matchModel(response.data, models.Token);
    setNormalToken(response.data.token);
  });

  return Promise.all([managerLogin, normalLogin]).catch(response =>
    failed(
      `登录失败，code: ${response.code}, message: ${response.message}。请清空数据库 cache 表，并重新测试`,
    ),
  );
});

// 测试完成，删除 token
after(() => {
  removeManagerToken();
  removeNormalToken();
  removeDefaultToken();
});
