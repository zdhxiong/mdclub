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
  getList(data, success, error) {
    get('/users', data, success, error);
  },

  /**
   * 获取指定用户信息
   */
  getOne(user_id, success, error) {
    get(`/users/${user_id}`, success, error);
  },

  /**
   * 获取当前登录用户信息
   */
  getMe(success, error) {
    get('/user', success, error);
  },

  /**
   * 更新当前登陆用户信息
   */
  updateMe(headline, success, error) {
    patch('/user', { headline }, success, error);
  },

  /**
   * 上传当前登录用户的头像
   */
  uploadMyAvatar(file, success, error) {
    const data = new FormData();
    data.append('avatar', file);

    post('/user/avatar', data, success, error);
  },

  /**
   * 删除当前登录用户的头像
   */
  deleteMyAvatar(success, error) {
    del('/user/avatar', success, error);
  },

  /**
   * 删除指定用户的头像
   */
  deleteAvatar(user_id, success, error) {
    del(`/users/${user_id}/avatar`, success, error);
  },

  /**
   * 上传当前登录用户的封面
   */
  uploadMyCover(file, success, error) {
    const data = new FormData();
    data.append('cover', file);

    post('/user/cover', data, success, error);
  },

  /**
   * 删除当前登录用户的封面
   */
  deleteMyCover(success, error) {
    del('/user/cover', success, error);
  },

  /**
   * 删除指定用户的封面
   */
  deleteCover(user_id, success, error) {
    del(`/users/${user_id}/cover`, success, error);
  },

  /**
   * 获取当前登录用户的关注者
   */
  getMyFollowers(data, success, error) {
    get('/user/followers', data, success, error);
  },

  /**
   * 获取指定用户的关注者
   */
  getFollowers(user_id, data, success, error) {
    get(`/users/${user_id}/followers`, data, success, error);
  },

  /**
   * 获取当前登录用户关注的用户
   */
  getMyFollowing(data, success, error) {
    get('/user/following', data, success, error);
  },

  /**
   * 获取当前登录用户未关注的用户
   */
  getMyNotFollowing(data, success, error) {
    get('/user/not_following', data, success, error);
  },

  /**
   * 获取指定用户关注的用户
   */
  getFollowing(user_id, data, success, error) {
    get(`/users/${user_id}/following`, data, success, error);
  },

  /**
   * 获取指定用户未关注的用户
   */
  getNotFollowing(user_id, data, success, error) {
    get(`/users/${user_id}/not_following`, data, success, error);
  },

  /**
   * 检查当前登录用户是否关注了指定用户
   */
  isMyFollowing(target_user_id, success, error) {
    get(`/user/following/${target_user_id}`, success, error);
  },

  /**
   * 检查指定用户是否关注了另一用户
   */
  isFollowing(user_id, target_user_id, success, error) {
    get(`/users/${user_id}/following/${target_user_id}`, success, error);
  },

  /**
   * 添加关注
   */
  addFollow(user_id, success, error) {
    put(`/user/following/${user_id}`, success, error);
  },

  /**
   * 取消关注
   */
  deleteFollow(user_id, success, error) {
    del(`/user/following/${user_id}`, success, error);
  },

  /**
   * 发送密码重置邮件
   */
  sendResetEmail(data, success, error) {
    post('/user/password/email', data, success, error);
  },

  /**
   * 验证邮箱并更新用户密码
   */
  updatePasswordByEmail(_data, success, error) {
    const data = _data;
    data.password = sha1(data.password);
    put('user/password', data, success, error);
  },

  /**
   * 验证邮箱并创建账号
   */
  create(_data, success, error) {
    const data = _data;
    data.password = sha1(data.password);
    post('/users', data, success, error);
  },

  /**
   * 发送注册验证邮件
   * @param data
   * @param success
   * @param error
   */
  sendRegisterEmail(data, success, error) {
    post('/user/register/email', data, success, error);
  },
};
