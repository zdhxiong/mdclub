export default {
  // 提问ID
  question_id: 0,

  // 若为回答详情页，则包含该参数
  answer_id: 0,

  // 当前访问的提问信息
  question: null,

  // 是否正在加载提问数据，提问详情页和回答详情页公用
  loading: false,

  // 是否正在变更关注状态
  following_question: false,

  // 排序方式
  answer_order: '-vote_count',

  // 回答列表，回答详情页的数据也放在这里
  answer_data: [],

  // 回答分页信息，为 null 表示未加载初始数据
  answer_pagination: null,

  // 是否正在加载回答，提问详情页和回答详情页公用
  answer_loading: false,

  // 是否正在发表回答
  answer_publishing: false,

  auto_save_key: 'page-answers',
};
