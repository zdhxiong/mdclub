export const tabs = ['questions', 'answers', 'articles'];

const state = {
  // 被访问者的用户信息
  interviewee: null,

  // 是否正在加载
  loading: false,

  // 是否正在更新被访问者的关注状态
  following_interviewee: false,

  // 是否正在提交个人信息
  edit_info_submitting: false,

  // 是否把详细信息折叠
  profile_fold: true,

  // 选项卡数组
  tabs,

  // 当前所处的选项卡
  current_tab: '',

  // 最后访问的文章ID
  last_visit_article_id: 0,

  // 最后访问的提问ID
  last_visit_question_id: 0,

  // 最后访问的回答ID
  last_visit_answer_id: 0,
};

tabs.forEach((tabName) => {
  if (tabName === 'questions') {
    state[`${tabName}_order`] = '-update_time';
  } else {
    state[`${tabName}_order`] = '-create_time';
  }

  state[`${tabName}_data`] = [];
  state[`${tabName}_pagination`] = null;
  state[`${tabName}_loading`] = false;
});

export default state;
