export default {
  data: [], // 数据列表
  order: '', // 排序方式
  pagination: {
    page: 1,
    per_page: 0, // 每页行数。若没有该值，则需要从 localStorage 中读取；若 localStorage 中也没有，则赋予默认值。
    previous: null,
    next: null,
    total: 0,
    pages: 0,
  },
  loading: false, // 是否正在加载
};
