export default {
  data: [],
  pagination: null, // 为 null 时表示未加载初始数据
  loading: false,
  count: null, // 通过 /notifications/count 接口获取的未读消息数量
};
