import sha1 from 'sha-1';
import {
  get,
  post,
  patch,
  put,
  del,
} from './util/requestAlias';

export default {
  /**
   * 获取用户列表
   *
   * GET /users
   */
  getList(data, success) {
    get('/users', data, success);
  },

  /**
   * 删除多个用户
   *
   * DELETE /users
   */
  disableMultiple(user_id, success) {
    del('/users/', { user_id }, success);
  },

  /**
   * 获取指定用户信息
   *
   * GET /users/{user_id}
   */
  getOne(user_id, success) {
    get(`/users/${user_id}`, success);
  },

  /**
   * 更新指定用户信息
   *
   * PATCH /users/{user_id}
   */
  updateOne(user_id, data, success) {
    patch(`/users/${user_id}`, data, success);
  },

  /**
   * 禁用指定用户
   *
   * DELETE /users/{user_id}
   */
  disableOne(user_id, success) {
    del(`/users/${user_id}`, success);
  },

  /**
   * 获取当前登录用户信息
   *
   * GET /user
   */
  getMe(success) {
    get('/user', success);
  },

  /**
   * 更新当前登陆用户信息
   *
   * PATCH /user
   */
  updateMe(data, success) {
    patch('/user', data, success);
  },

  /**
   * 删除指定用户的头像
   *
   * DELETE /users/{user_id}/avatar
   */
  deleteAvatar(user_id, success) {
    del(`/users/${user_id}/avatar`, success);
  },

  /**
   * 上传当前登录用户的头像
   *
   * POST /user/avatar
   */
  uploadMyAvatar(file, success) {
    const data = new FormData();
    data.append('avatar', file);

    post('/user/avatar', data, success);
  },

  /**
   * 删除当前登录用户的头像
   *
   * DELETE /user/avatar
   */
  deleteMyAvatar(success) {
    del('/user/avatar', success);
  },

  /**
   * 删除指定用户的封面
   *
   * DELETE /users/{user_id}/cover
   */
  deleteCover(user_id, success) {
    del(`/users/${user_id}/cover`, success);
  },

  /**
   * 上传当前登录用户的封面
   *
   * POST /user/cover
   */
  uploadMyCover(file, success) {
    const data = new FormData();
    data.append('cover', file);

    post('/user/cover', data, success);
  },

  /**
   * 删除当前登录用户的封面
   *
   * DELETE /user/cover
   */
  deleteMyCover(success) {
    del('/user/cover', success);
  },

  /**
   * 获取指定用户的关注者
   *
   * GET /users/{user_id}/followers
   */
  getFollowers(user_id, data, success) {
    get(`/users/${user_id}/followers`, data, success);
  },

  /**
   * 添加关注
   *
   * POST /users/{user_id}/followers
   */
  addFollow(user_id, success) {
    post(`/users/${user_id}/followers`, success);
  },

  /**
   * 取消关注
   *
   * DELETE /users/{user_id}/followers
   */
  deleteFollow(user_id, success) {
    del(`/users/${user_id}/followers`, success);
  },

  /**
   * 获取指定用户关注的用户
   *
   * GET /users/{user_id}/followees
   */
  getFollowees(user_id, data, success) {
    get(`/users/${user_id}/followees`, data, success);
  },

  /**
   * 获取当前登录用户的关注者
   *
   * GET /user/followers
   */
  getMyFollowers(data, success) {
    get('/user/followers', data, success);
  },

  /**
   * 获取当前登录用户关注的用户
   *
   * GET /user/followees
   */
  getMyFollowees(data, success) {
    get('/user/followees', data, success);
  },

  /**
   * 发送密码重置邮件
   *
   * POST /user/password/email
   */
  sendResetEmail(data, success) {
    post('/user/password/email', data, success);
  },

  /**
   * 验证邮箱并更新用户密码
   *
   * PUT /user/password
   */
  updatePasswordByEmail(_data, success) {
    const data = _data;
    data.password = sha1(data.password);
    put('user/password', data, success);
  },

  /**
   * 验证邮箱并创建账号
   *
   * POST /users
   */
  create(_data, success) {
    const data = _data;
    data.password = sha1(data.password);
    post('/users', data, success);
  },

  /**
   * 发送注册验证邮件
   *
   * POST /user/register/email
   */
  sendRegisterEmail(data, success) {
    post('/user/register/email', data, success);
  },

  /**
   * 获取已禁用的用户列表
   *
   * GET /trash/users
   */
  getDisabledList(data, success) {
    get('/trash/users', data, success);
  },

  /**
   * 启用多个已禁用的用户
   *
   * POST /trash/users
   */
  enableMultiple(user_id, success) {
    post('/trash/users', { user_id }, success);
  },

  /**
   * 启用指定用户
   *
   * POST /trash/users/{user_id}
   */
  enableOne(user_id, success) {
    post(`/trash/users/${user_id}`, success);
  },
};
