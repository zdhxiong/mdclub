import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  // 获取指定用户发表的评论
  getListByUserId: (user_id, data) => get(`/users/${user_id}/comments`, data),

  // 获取当前用户发表的评论
  getMyList: data => get('/user/comments', data),

  // 获取评论列表
  getList: data => get('/comments', data),

  // 删除多个评论
  deleteMultiple: comment_id => del('/comments', { comment_id }),

  // 获取指定评论
  getOne: comment_id => get(`/comments/${comment_id}`),

  // 修改指定评论
  updateOne: (comment_id, data) => patch(`/comments/${comment_id}`, data),

  // 删除指定评论
  deleteOne: comment_id => del(`/comments/${comment_id}`),

  // 获取指定评论的投票者
  getVoters: (comment_id, data) => get(`/comments/${comment_id}/voters`, data),

  // 添加投票
  addVote: (comment_id, data) => post(`/comments/${comment_id}/voters`, data),

  // 取消投票
  deleteVote: comment_id => del(`/comments/${comment_id}/voters`),

  // 获取已删除的评论列表
  getDeletedList: data => get('/trash/comments', data),

  // 恢复多个评论
  restoreMultiple: comment_id => post('/trash/comments', { comment_id }),

  // 销毁已删除的多个评论
  destroyMultiple: comment_id => del('/trash/comments', { comment_id }),

  // 恢复指定评论
  restoreOne: comment_id => post(`/trash/comments/${comment_id}`),

  // 销毁指定的已删除的评论
  destroyOne: comment_id => del(`/trash/comments/${comment_id}`),
};
