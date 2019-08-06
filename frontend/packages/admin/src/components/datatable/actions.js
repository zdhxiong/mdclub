import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import actionsAbstract from '../../abstracts/actions/component';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;

    // 表格滚动时，表头阴影变化
    const $tbody = $(props.element).find('tbody');
    const onScroll = e => actions.setState({ isScrollTop: !e.target.scrollTop });

    $tbody.on('scroll', onScroll);
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
      isScrollTop: true,
    });
  },

  /**
   * 开始加载数据
   */
  loadStart: () => (state, actions) => {
    actions.setState({ data: [], loading: true });
  },

  /**
   * 数据加载失败
   */
  loadFail: ({ message }) => (state, actions) => {
    actions.setState({ loading: false });
    mdui.snackbar(message);
  },

  /**
   * 数据加载成功
   */
  loadSuccess: ({ data, pagination }) => (state, actions) => {
    // todo 用 ramda 优化
    const isCheckedRows = {};
    data.map((item) => {
      isCheckedRows[item[state.primaryKey]] = false;
    });

    actions.setState({
      loading: false,
      isCheckedRows,
      isCheckedAll: false,
      checkedCount: 0,
      data,
    });

    global_actions.components.pagination.setState(pagination);
  },

  /**
   * 更新某一行的数据
   */
  updateOne: row => (state, actions) => {
    const data = state.data;

    // todo 用 ramda 优化
    data.forEach((item, index) => {
      if (item[state.primaryKey] === row[state.primaryKey]) {
        data[index] = row;
      }
    });

    actions.setState({ data });
  },

  /**
   * 切换某一行的选中状态
   */
  checkOne: rowId => (state, actions) => {
    const { isCheckedRows } = state;
    isCheckedRows[rowId] = !isCheckedRows[rowId];

    let checkedCount = 0;
    let isCheckedAll = true;

    // todo 用 ramda 优化
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

    // todo 用 ramda 优化
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
