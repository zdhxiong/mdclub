import sha1 from 'sha-1';
import {
  get,
  post,
  patch,
  put,
  del,
} from './util/requestAlias';

export default {
  // 获取用户列表
  getList: data => get('/users', data),

  // 删除多个用户
  disableMultiple: user_id => del('/users/', { user_id }),

  // 获取指定用户信息
  getOne: user_id => get(`/users/${user_id}`),

  // 更新指定用户信息
  updateOne: (user_id, data) => patch(`/users/${user_id}`, data),

  // 禁用指定用户
  disableOne: user_id => del(`/users/${user_id}`),

  // 获取当前登录用户信息
  getMe: () => get('/user'),

  // 更新当前登陆用户信息
  updateMe: data => patch('/user', data),

  // 删除指定用户的头像
  deleteAvatar: user_id => del(`/users/${user_id}/avatar`),

  // 上传当前登录用户的头像
  uploadMyAvatar: (file) => {
    const data = new FormData();
    data.append('avatar', file);

    return post('/user/avatar', data);
  },

  // 删除当前登录用户的头像
  deleteMyAvatar: () => del('/user/avatar'),

  // 删除指定用户的封面
  deleteCover: user_id => del(`/users/${user_id}/cover`),

  // 上传当前登录用户的封面
  uploadMyCover: (file) => {
    const data = new FormData();
    data.append('cover', file);

    return post('/user/cover', data);
  },

  // 删除当前登录用户的封面
  deleteMyCover: () => del('/user/cover'),

  // 获取指定用户的关注者
  getFollowers: (user_id, data) => get(`/users/${user_id}/followers`, data),

  // 添加关注
  addFollow: user_id => post(`/users/${user_id}/followers`),

  // 取消关注
  deleteFollow: user_id => del(`/users/${user_id}/followers`),

  // 获取指定用户关注的用户
  getFollowees: (user_id, data) => get(`/users/${user_id}/followees`, data),

  // 获取当前登录用户的关注者
  getMyFollowers: data => get('/user/followers', data),

  // 获取当前登录用户关注的用户
  getMyFollowees: data => get('/user/followees', data),

  // 发送密码重置邮件
  sendResetEmail: data => post('/user/password/email', data),

  // 验证邮箱并更新用户密码
  updatePasswordByEmail: (_data) => {
    const data = _data;
    data.password = sha1(data.password);

    return put('user/password', data);
  },

  // 验证邮箱并创建账号
  create: (_data) => {
    const data = _data;
    data.password = sha1(data.password);

    return post('/users', data);
  },

  // 发送注册验证邮件
  sendRegisterEmail: data => post('/user/register/email', data),

  // 获取已禁用的用户列表
  getDisabledList: data => get('/trash/users', data),

  // 启用多个已禁用的用户
  enableMultiple: user_id => post('/trash/users', { user_id }),

  // 启用指定用户
  enableOne: user_id => post(`/trash/users/${user_id}`),
};
