export default {
  // 首次加载全部数据
  loading: false,

  // 这些信息仅首次一起加载
  system_info: null,
  total_user: null,
  total_question: null,
  total_article: null,
  total_answer: null,
  total_comment: null,

  // 这些数据可以单独加载
  new_user: {
    loading: false,
    data: null,
    range: '7day',
  },
  new_question: {
    loading: false,
    data: null,
    range: '7day',
  },
  new_article: {
    loading: false,
    data: null,
    range: '7day',
  },
  new_answer: {
    loading: false,
    data: null,
    range: '7day',
  },
  new_comment: {
    loading: false,
    data: null,
    range: '7day',
  },
};
