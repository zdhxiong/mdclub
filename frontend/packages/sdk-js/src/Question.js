import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {

  // 获取指定用户发表的提问列表
  getListByUserId: (user_id, data) => get(`/users/${user_id}/questions`, data),

  // 获取当前用户发表的提问列表
  getMyList: data => get('/user/questions', data),

  // 获取指定话题下的提问列表
  getListByTopicId: (topic_id, data) => get(`/topics/${topic_id}/questions`, data),

  // 获取提问列表
  getList: data => get('/questions', data),

  // 创建提问
  create: data => post('/questions', data),

  // 删除多个提问
  deleteMultiple: question_id => del('/questions', { question_id }),

  // 获取指定提问信息
  getOne: question_id => get(`/questions/${question_id}`),

  // 更新指定提问
  updateOne: (question_id, data) => patch(`/questions/${question_id}`, data),

  // 删除指定提问
  deleteOne: question_id => del(`/questions/${question_id}`),

  // 获取评论列表
  getComments: (question_id, data) => get(`/questions/${question_id}/comments`, data),

  // 发表评论
  addComment: (question_id, data) => post(`/questions/${question_id}/comments`, data),

  // 获取投票者列表
  getVoters: (question_id, data) => get(`/questions/${question_id}/voters`, data),

  // 添加投票
  addVote: (question_id, data) => post(`/questions/${question_id}/voters`, data),

  // 取消投票
  deleteVote: question_id => del(`/questions/${question_id}/voters`),

  // 获取指定用户关注的提问列表
  getFollowing: (user_id, data) => get(`/users/${user_id}/following_questions`, data),

  // 获取登录用户关注的提问
  getMyFollowing: data => get('/user/following_questions', data),

  // 获取指定提问的关注者
  getFollowers: (question_id, data) => get(`/questions/${question_id}/followers`, data),

  // 添加关注
  addFollow: question_id => post(`/questions/${question_id}/followers`),

  // 取消关注
  deleteFollow: question_id => del(`/questions/${question_id}/followers`),

  // 获取已删除的提问列表
  getDeletedList: data => get('/trash/questions', data),

  // 恢复多个提问
  restoreMultiple: question_id => post('/trash/questions', { question_id }),

  // 销毁已删除的多个提问
  destroyMultiple: question_id => del('/trash/questions', { question_id }),

  // 恢复指定提问
  restoreOne: question_id => post(`/trash/questions/${question_id}`),

  // 销毁指定的已删除的提问
  destroyOne: question_id => del(`/trash/questions/${question_id}`),
};
