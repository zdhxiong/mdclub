import sha1 from 'sha-1';
import {
  post,
} from './util/requestAlias';

export default {
  // 生成 token
  create: (_data) => {
    const data = _data;
    data.password = sha1(data.password);

    return post('/tokens', data);
  },
};
