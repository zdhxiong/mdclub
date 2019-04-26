import {
  get,
  post,
  patch,
  del,
} from './util/requestAlias';

export default {
  // 获取指定用户发表的文章列表
  getListByUserId: (user_id, data) => get(`/users/${user_id}/articles`, data),

  // 获取我发表的文章列表
  getMyList: data => get('/user/articles', data),

  // 获取指定话题下的文章列表
  getListByTopicId: (topic_id, data) => get(`/topics/${topic_id}/articles`, data),

  // 获取文章列表
  getList: data => get('/articles', data),

  // 发表文章
  create: data => post('/articles', data),

  // 删除多个文章
  deleteMultiple: article_id => del('/articles', { article_id }),

  // 获取指定文章信息
  getOne: article_id => get(`/articles/${article_id}`),

  // 修改指定文章
  updateOne: (article_id, data) => patch(`/articles/${article_id}`, data),

  // 删除指定文章
  deleteOne: article_id => del(`/articles/${article_id}`),

  // 获取评论列表
  getComments: (article_id, data) => get(`/articles/${article_id}/comments`, data),

  // 发表评论
  addComment: (article_id, data) => post(`/articles/${article_id}/comments`, data),

  // 获取投票者列表
  getVoters: (article_id, data) => get(`/articles/${article_id}/voters`, data),

  // 添加投票
  addVote: (article_id, data) => post(`/articles/${article_id}/voters`, data),

  // 取消投票
  deleteVote: article_id => del(`/articles/${article_id}/voters`),

  // 获取指定用户关注的文章列表
  getFollowing: (user_id, data) => get(`/users/${user_id}/following_articles`, data),

  // 获取当前用户关注的文章列表
  getMyFollowing: data => get('/user/following_articles', data),

  // 获取指定文章的关注者
  getFollowers: (article_id, data) => get(`/articles/${article_id}/followers`, data),

  // 添加关注
  addFollow: article_id => post(`/articles/${article_id}/followers`),

  // 取消关注
  deleteFollow: article_id => del(`/articles/${article_id}/followers`),

  // 获取已删除的文章列表
  getDeletedList: data => get('/trash/articles', data),

  // 恢复多个文章
  restoreMultiple: article_id => post('/trash/articles', { article_id }),

  // 销毁已删除的多个文章
  destroyMultiple: article_id => del('/trash/articles', { article_id }),

  // 恢复指定文章
  restoreOne: article_id => post(`/trash/articles/${article_id}`),

  // 销毁指定的已删除的文章
  destroyOne: article_id => del(`/trash/articles/${article_id}`),
};
