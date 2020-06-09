import extend from 'mdui.jq/es/functions/extend';
import editorState from '~/components/editor/state';
import topicSelectorState from '~/components/editor/components/topic-selector/state';

export const tabs = ['recent', 'popular', 'following'];

const state = {
  // 当前所处的选项卡
  current_tab: '',

  // 是否正在发表提问
  publishing: false,

  // 选项卡数组
  tabs,

  // 最后访问的提问ID
  last_visit_id: 0,

  // 自动保存草稿的键名前缀
  auto_save_key: 'page-questions',
};

tabs.forEach((tabName) => {
  state[`${tabName}_data`] = [];
  state[`${tabName}_pagination`] = null; // 为 null 表示未加载初始数据
  state[`${tabName}_loading`] = false;
});

export default extend(state, editorState, topicSelectorState);
