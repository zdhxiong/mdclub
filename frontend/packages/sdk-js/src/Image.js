import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取图片列表
   *
   * GET /images
   */
  getList(data, success) {
    get('/images', data, success);
  },

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

  /**
   * 删除多张图片
   *
   * DELETE /images
   */
  deleteMultiple(hash, success) {
    del('/images', { hash }, success);
  },

  /**
   * 获取指定图片信息
   *
   * GET /images/{hash}
   */
  getOne(hash, success) {
    get(`/images/${hash}`, success);
  },

  /**
   * 更新指定图片信息
   *
   * PATCH /images/{hash}
   */
  updateOne(hash, data, success) {
    patch(`/images/${hash}`, data, success);
  },

  /**
   * 删除指定图片
   *
   * DELETE /images/{hash}
   */
  deleteOne(hash, success) {
    del(`/images/${hash}`, success);
  },
};
