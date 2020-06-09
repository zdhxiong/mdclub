/**
 * 在含编辑器，且编辑器中含话题选择器的页面中，引入该 state
 */
export default {
  // 加载的话题数据
  topics_data: [],

  // 分页
  topics_pagination: null,

  // 是否加载中
  topics_loading: false,

  // 最多可选 10 个
  topics_max_selectable: 10,
};
