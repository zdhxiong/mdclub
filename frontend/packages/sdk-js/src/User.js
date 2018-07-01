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
   */
  getList(data, success) {
    get('/users', data, success);
  },

  /**
   * 获取指定用户信息
   */
  getOne(user_id, success) {
    get(`/users/${user_id}`, success);
  },

  /**
   * 获取当前登录用户信息
   */
  getMe(success) {
    get('/user', success);
  },

  /**
   * 更新当前登陆用户信息
   */
  updateMe(headline, success) {
    patch('/user', { headline }, success);
  },

  /**
   * 上传当前登录用户的头像
   */
  uploadMyAvatar(file, success) {
    const data = new FormData();
    data.append('avatar', file);

    post('/user/avatar', data, success);
  },

  /**
   * 删除当前登录用户的头像
   */
  deleteMyAvatar(success) {
    del('/user/avatar', success);
  },

  /**
   * 删除指定用户的头像
   */
  deleteAvatar(user_id, success) {
    del(`/users/${user_id}/avatar`, success);
  },

  /**
   * 上传当前登录用户的封面
   */
  uploadMyCover(file, success) {
    const data = new FormData();
    data.append('cover', file);

    post('/user/cover', data, success);
  },

  /**
   * 删除当前登录用户的封面
   */
  deleteMyCover(success) {
    del('/user/cover', success);
  },

  /**
   * 删除指定用户的封面
   */
  deleteCover(user_id, success) {
    del(`/users/${user_id}/cover`, success);
  },

  /**
   * 获取当前登录用户的关注者
   */
  getMyFollowers(data, success) {
    get('/user/followers', data, success);
  },

  /**
   * 获取指定用户的关注者
   */
  getFollowers(user_id, data, success) {
    get(`/users/${user_id}/followers`, data, success);
  },

  /**
   * 获取当前登录用户关注的用户
   */
  getMyFollowing(data, success) {
    get('/user/following', data, success);
  },

  /**
   * 获取当前登录用户未关注的用户
   */
  getMyNotFollowing(data, success) {
    get('/user/not_following', data, success);
  },

  /**
   * 获取指定用户关注的用户
   */
  getFollowing(user_id, data, success) {
    get(`/users/${user_id}/following`, data, success);
  },

  /**
   * 获取指定用户未关注的用户
   */
  getNotFollowing(user_id, data, success) {
    get(`/users/${user_id}/not_following`, data, success);
  },

  /**
   * 检查当前登录用户是否关注了指定用户
   */
  isMyFollowing(target_user_id, success) {
    get(`/user/following/${target_user_id}`, success);
  },

  /**
   * 检查指定用户是否关注了另一用户
   */
  isFollowing(user_id, target_user_id, success) {
    get(`/users/${user_id}/following/${target_user_id}`, success);
  },

  /**
   * 添加关注
   */
  addFollow(user_id, success) {
    put(`/user/following/${user_id}`, success);
  },

  /**
   * 取消关注
   */
  deleteFollow(user_id, success) {
    del(`/user/following/${user_id}`, success);
  },

  /**
   * 发送密码重置邮件
   */
  sendResetEmail(data, success) {
    post('/user/password/email', data, success);
  },

  /**
   * 验证邮箱并更新用户密码
   */
  updatePasswordByEmail(_data, success) {
    const data = _data;
    data.password = sha1(data.password);
    put('user/password', data, success);
  },

  /**
   * 验证邮箱并创建账号
   */
  create(_data, success) {
    const data = _data;
    data.password = sha1(data.password);
    post('/users', data, success);
  },

  /**
   * 发送注册验证邮件
   */
  sendRegisterEmail(data, success) {
    post('/user/register/email', data, success);
  },
};
