import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  // 获取图片列表
  getList: data => get('/images', data),

  // 上传图片
  upload: (file) => {
    const data = new FormData();
    data.append('file', file);

    return post('/images', data);
  },

  // 删除多张图片
  deleteMultiple: hash => del('/images', { hash }),

  // 获取指定图片信息
  getOne: hash => get(`/images/${hash}`),

  // 更新指定图片信息
  updateOne: (hash, data) => patch(`/images/${hash}`, data),

  // 删除指定图片
  deleteOne: hash => del(`/images/${hash}`),
};
