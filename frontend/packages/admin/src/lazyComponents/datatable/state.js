let per_page = window.localStorage.getItem('admin_per_page');
per_page = per_page ? parseInt(per_page, 10) : 10;

export default {
  /**
   * 列定义
   * [
   *   {
   *     title: '列头标题',
   *     field: '字段名称',
   *     type: '字段类型：time、string、number、html、relation'
   *     onClick: function // type 为 relation 时需要
   *   }
   * ]
   */
  columns: [],
  /**
   * 操作项定义
   * [
   *   {
   *     type: '类型: target、btn',
   *     getTargetLink: function, // type 为 target 时，需要该函数返回链接
   *     label: '提示文本'   // type 为 target 时需要
   *     icon: '图标'       // type 为 target 时需要
   *     onClick: function // type 为 target 时需要
   *   }
   * ]
   */
  actions: [],
  /**
   * 批量操作项
   * [
   *   {
   *     label: '提示文本',
   *     icon: '图标',
   *     onClick: function
   *   }
   * ]
   */
  batchActions: [],
  primaryKey: '', // 主键字段名
  onRowClick: false, // 点击行执行的函数

  isCheckedRows: {}, // 键名为对象ID，键值为 bool 值
  isCheckedAll: false, // 是否已全部选中
  checkedCount: 0,

  data: [], // 接口返回的原始数据
  order: '', // 排序方式
  pagination: {
    page: 1,
    per_page, // 每页行数
    previous: null,
    next: null,
    total: 0,
    pages: 0,
  },
  loading: false, // 是否正在加载
};
