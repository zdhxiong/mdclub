import mdui, { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/actions/lazyComponent';

let global_actions;
let $element;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    $element = $(props.element);

    // 表格滚动时，表头阴影变化
    $element.find('tbody').on('scroll', (e) => {
      if (e.target.scrollTop) {
        $element.addClass('is-top');
      } else {
        $element.removeClass('is-top');
      }
    });
  },

  /**
   * 销毁前执行
   */
  destroy: () => (state, actions) => {
    actions.reset();
  },

  /**
   * 重置状态
   */
  reset: () => (state, actions) => {
    actions.setState({
      columns: [],
      buttons: [],
      batchButtons: [],
      orders: [],
      order: '',
      primaryKey: '',
      onRowClick: false,
      isCheckedRows: {},
      isCheckedAll: false,
      checkedCount: 0,
      data: [],
      loading: false,
    });
  },

  /**
   * 开始加载数据
   */
  loadStart: () => (state, actions) => {
    actions.setState({
      data: [],
      loading: true,
    });
  },

  /**
   * 数据加载完成
   */
  loadEnd: response => (state, actions) => {
    actions.setState({ loading: false });

    if (response.code) {
      mdui.snackbar(response.message);
      return;
    }

    const isCheckedRows = {};
    response.data.map((item) => {
      isCheckedRows[item[state.primaryKey]] = false;
    });

    actions.setState({
      isCheckedRows,
      isCheckedAll: false,
      checkedCount: 0,
      data: response.data,
    });

    global_actions.lazyComponents.pagination.setState(response.pagination);
  },

  /**
   * 切换某一行的选中状态
   */
  checkOne: rowId => (state, actions) => {
    const { isCheckedRows } = state;
    isCheckedRows[rowId] = !isCheckedRows[rowId];

    let checkedCount = 0;
    let isCheckedAll = true;

    Object.keys(isCheckedRows).map((_rowId) => {
      if (!isCheckedRows[_rowId]) {
        isCheckedAll = false;
      } else {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },

  /**
   * 切换全部行的选中状态
   */
  checkAll: e => (state, actions) => {
    let checkedCount = 0;
    const isCheckedAll = e.target.checked;
    const { isCheckedRows } = state;

    Object.keys(isCheckedRows).map((_rowId) => {
      isCheckedRows[_rowId] = isCheckedAll;

      if (isCheckedAll) {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },

  /**
   * 修改排序方式
   */
  changeOrder: ({ e, order, onChange }) => (state, actions) => {
    e.preventDefault();

    actions.setState({ order });

    onChange();
  },
});
