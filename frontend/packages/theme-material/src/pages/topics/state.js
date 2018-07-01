const tabs = ['following', 'recommended'];

const state = {
  current_tab: '', // 当前所处选项卡 following、recommended
  tabs,
};

tabs.forEach((tab) => {
  state[`${tab}_data`] = [];
  state[`${tab}_pagination`] = false; // 为false表示未加载初始数据
  state[`${tab}_loading`] = false;
});

export default state;
