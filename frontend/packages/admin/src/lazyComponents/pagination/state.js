export default {
  page: 1, // 当前页码
  per_page: 0, // 每页行数。若没有该值，则需要从 localStorage 中读取；localStorage 中也没有，则赋予默认值
  previous: null, // 上一页页码
  next: null, // 下一页页码
  total: 0, // 总行数
  pages: 0, // 总页数
  loading: false, // 是否正在加载中
};
