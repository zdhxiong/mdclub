let per_page = window.localStorage.getItem('admin_per_page');
per_page = per_page ? parseInt(per_page, 10) : 25;

export default {
  page: 1,
  per_page, // 每页行数
  previous: null,
  next: null,
  total: 0,
  pages: 0,
};
