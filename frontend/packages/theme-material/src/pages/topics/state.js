export const tabs = ['following', 'recommended'];

const state = {
  // 当前所处选项卡 following、recommended
  current_tab: '',

  // 选项卡数组
  tabs,

  // 当前激活的选项卡索引号
  tabIndex: tabs.indexOf('recommended'),
};

tabs.forEach((tabName) => {
  state[`${tabName}_data`] = [];
  state[`${tabName}_pagination`] = null; // 为 null 表示未加载初始数据
  state[`${tabName}_loading`] = false;
});

export default state;
