export default {
  // 评论目标类型：article, question, answer
  commentable_type: '',

  // 评论目标的ID
  commentable_id: 0,

  // 排序方式
  order: 'create_time',

  // 评论列表
  comments_data: [],

  // 评论分页信息，为 null 表示未加载初始数据
  pagination: null,

  // 是否正在加载评论
  loading: false,

  // 是否正在发表评论
  submitting: false,

  // 是否打开评论模态框
  open_dialog: false,
};
