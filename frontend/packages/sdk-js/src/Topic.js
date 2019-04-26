import {
  get,
  post,
  del,
} from './util/requestAlias';

export default {
  // 获取话题列表
  getList: data => get('/topics', data),

  // 发布话题
  create: (name, description, cover) => {
    const data = new FormData();
    data.append('name', name);
    data.append('description', description);
    data.append('cover', cover);

    return post('/topics', data);
  },

  // 删除多个话题
  deleteMultiple: topic_id => del('/topics', { topic_id }),

  // 获取指定话题信息
  getOne: topic_id => get(`/topics/${topic_id}`),

  // 更新话题信息
  updateOne: (topic_id, params) => {
    const data = new FormData();

    if (typeof params.name !== 'undefined') {
      data.append('name', params.name);
    }

    if (typeof params.description !== 'undefined') {
      data.append('description', params.description);
    }

    if (typeof params.cover !== 'undefined') {
      data.append('cover', params.cover);
    }

    return post(`/topics/${topic_id}`, data);
  },

  // 删除指定话题
  deleteOne: topic_id => del(`/topics/${topic_id}`),

  // 获取指定用户关注的话题列表
  getFollowing: (user_id, data) => get(`/users/${user_id}/following_topics`, data),

  // 获取当前用户关注的话题列表
  getMyFollowing: data => get('/user/following_topics', data),

  // 获取指定话题的关注者
  getFollowers: (topic_id, data) => get(`/topics/${topic_id}/followers`, data),

  // 添加关注
  addFollow: topic_id => post(`/topics/${topic_id}/followers`),

  // 取消关注
  deleteFollow: topic_id => del(`/topics/${topic_id}/followers`),

  // 获取已删除的话题列表
  getDeletedList: data => get('/trash/topics', data),

  // 恢复多个话题
  restoreMultiple: topic_id => post('/trash/topics', { topic_id }),

  // 销毁已删除的多个话题
  destroyMultiple: topic_id => del('/trash/topics', { topic_id }),

  // 恢复指定话题
  restoreOne: topic_id => post(`/trash/topics/${topic_id}`),

  // 销毁指定的已删除的话题
  destroyOne: topic_id => del(`/trash/topics/${topic_id}`),
};
