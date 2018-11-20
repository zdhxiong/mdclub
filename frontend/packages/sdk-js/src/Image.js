import {
  post,
} from './util/requestAlias';

export default {
  /**
   * 上传图片
   *
   * POST /images
   */
  upload(file, success) {
    const data = new FormData();
    data.append('file', file);

    post('/images', data, success);
  },
};
