import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  // 获取指定用户发表的回答
  getListByUserId: (user_id, data) => get(`/users/${user_id}/answers`, data),

  // 获取我发表的回答列表
  getMyList: data => get('/user/answers', data),

  // 获取指定提问下的回答列表
  getListByQuestionId: (question_id, data) => get(`/questions/${question_id}/answers`, data),

  // 创建回答
  create: (question_id, data) => post(`/questions/${question_id}/answers`, data),

  // 获取回答列表
  getList: data => get('/answers', data),

  // 删除多个回答
  deleteMultiple: answer_id => del('/answers', { answer_id }),

  // 获取指定回答信息
  getOne: answer_id => get(`/answers/${answer_id}`),

  // 修改指定回答
  updateOne: (answer_id, data) => patch(`/answers/${answer_id}`, data),

  // 删除指定回答
  deleteOne: answer_id => del(`/answers/${answer_id}`),

  // 获取评论列表
  getComments: (answer_id, data) => get(`/answers/${answer_id}/comments`, data),

  // 发表评论
  addComment: (answer_id, data) => post(`/answers/${answer_id}/comments`, data),

  // 获取投票者列表
  getVoters: (answer_id, data) => get(`/answers/${answer_id}/voters`, data),

  // 添加投票
  addVote: (answer_id, data) => post(`/answers/${answer_id}/voters`, data),

  // 取消投票
  deleteVote: answer_id => del(`/answers/${answer_id}/voters`),

  // 获取已删除的回答列表
  getDeletedList: data => get('/trash/answers', data),

  // 恢复多个回答
  restoreMultiple: answer_id => post('/trash/answers', { answer_id }),

  // 销毁已删除的多个回答
  destroyMultiple: answer_id => del('/trash/answers', { answer_id }),

  // 恢复指定回答
  restoreOne: answer_id => post(`/trash/answers/${answer_id}`),

  // 销毁指定的已删除的回答
  destroyOne: answer_id => del(`/trash/answers/${answer_id}`),
};
