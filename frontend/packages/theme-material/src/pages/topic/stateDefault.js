export const tabs = ['questions', 'articles'];

const state = {
  // 话题ID
  topic_id: 0,

  // 话题信息
  topic: null,

  // 是否正在加载话题信息
  loading: false,

  // 是否正在变更关注状态
  following_topic: false,

  // 选项卡数组
  tabs,

  // 当前所处的选项卡
  current_tab: '',

  // 最后访问的文章ID
  last_visit_article_id: 0,

  // 最后访问的提问ID
  last_visit_question_id: 0,
};

tabs.forEach((tabName) => {
  state[`${tabName}_order`] = '-update_time';
  state[`${tabName}_data`] = [];
  state[`${tabName}_pagination`] = null;
  state[`${tabName}_loading`] = false;
});

export default state;
