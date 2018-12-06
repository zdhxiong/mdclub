import mdui, { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/actions/lazyComponent';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
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
      actions: [],
      batchActions: [],
      primaryKey: '',
      isCheckedRows: {},
      isCheckedAll: false,
      checkedCount: 0,
      data: [],
      order: '',
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
      isCheckedRows[item[response.primaryKey]] = false;
    });

    actions.setState({
      columns: response.columns,
      actions: response.actions,
      batchActions: response.batchActions,
      primaryKey: response.primaryKey,
      onRowClick: typeof response.onRowClick !== 'undefined' ? response.onRowClick : false,
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
    const isCheckedRows = state.isCheckedRows;
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
    const isCheckedRows = state.isCheckedRows;

    Object.keys(isCheckedRows).map((_rowId) => {
      isCheckedRows[_rowId] = isCheckedAll;

      if (isCheckedAll) {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },
});
