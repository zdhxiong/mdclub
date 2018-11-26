export default {
  /**
   * 列定义
   * [
   *   {
   *     title: '列头标题',
   *     data: '字段名称',
   *     type: '字段类型：relation、time、string、html'
   *     relation: '关联类型：user'
   *     relation_id: '关联ID',
   *   }
   * ]
   */
  columns: [],
  /**
   * 操作项定义
   * [
   *   {
   *     type: '类型: link、btn',
   *     getLink: function, // type 为 link 时，需要该函数返回链接
   *     label: '提示文本'   // type 为 btn 时需要
   *     icon: '图标'       // type 为 btn 时需要
   *     onClick: function // type 为 btn 时需要
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

  isCheckedRows: {}, // 键名为对象ID，键值为 bool 值
  isCheckedAll: false, // 是否已全部选中
  checkedCount: 0,

  data: [], // 接口返回的原始数据
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
