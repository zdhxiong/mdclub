export default {
  /**
   * 列定义，最后一列将不显示标题，鼠标悬浮时替换为操作按钮
   * [
   *   {
   *     title: '列头标题',
   *     field: '字段名称',
   *     type: '字段类型：time、string、number、relation、handler'
   *     onClick: function // type 为 relation 时需要
   *     handler: function // type 为 handler 时需要
   *   }
   * ]
   */
  columns: [],

  /**
   * 操作项定义
   * [
   *   {
   *     type: '类型: target、btn', // target：新窗口打开页面，btn：绑定函数的按钮
   *     getTargetLink: function, // type 为 target 时，需要该函数返回链接
   *     label: '提示文本'   // type 为 btn 时需要
   *     icon: '图标'       // type 为 btn 时需要
   *     onClick: function // type 为 btn 时需要
   *   }
   * ]
   */
  buttons: [],

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
  batchButtons: [],

  /**
   * 排序方式定义
   * [
   *   {
   *     name: '提示文字'
   *     value: '排序参数值'
   *   }
   * ]
   */
  orders: [],

  /**
   * 当前排序方式
   *
   * NOTE: 该参数的 view 部分在 pagination 组件中
   */
  order: '',
  primaryKey: '', // 主键字段名
  onRowClick: false, // 点击行执行的函数

  isCheckedRows: {}, // 键名为对象ID，键值为 bool 值
  isCheckedAll: false, // 是否已全部选中
  checkedCount: 0,

  data: [], // 接口返回的原始数据
  loading: false, // 是否正在加载

  isScrollTop: true, // 表格是否滚动到了顶部
};
