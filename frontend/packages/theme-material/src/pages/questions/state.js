const tabs = ['recent', 'popular', 'following'];

const state = {
  current_tab: '', // 当前所处的选项卡
  publishing: false, // 是否正在发表问题
  tabs,
};

tabs.forEach((tab) => {
  state[`${tab}_data`] = [];
  state[`${tab}_pagination`] = false; // 为 false 表示未加载初始数据
  state[`${tab}_loading`] = false;
});

export default state;
