import mdui, { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/actions';

export default $.extend({}, actionsAbstract, {
  /**
   * 设置分页状态
   */
  pagination: {
    setState: value => (value),
    getState: () => state => state,
  },

  /**
   * 初始化
   */
  init: props => (state, actions) => {
    if (!state.pagination.per_page) {
      const per_page = window.localStorage.getItem('admin_per_page');

      actions.pagination.setState({
        per_page: per_page ? parseInt(per_page, 10) : 10,
      });
    }
  },

  /**
   * 销毁前执行
   */
  destroy: () => (state, actions) => {
    // 重置状态
    actions.setState({
      data: [],
      order: '',
      loading: false,
    });

    // per_page 状态需要保留，不重置
    actions.pagination.setState({
      page: 1,
      previous: null,
      next: null,
      total: 0,
      pages: 0,
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

    actions.setState({
      data: response.data,
      pagination: response.pagination,
    });
  },

  /**
   * 修改每页行数
   */
  onPerPageChange: ({ e, loadData }) => (state, actions) => {
    const per_page = e.target.value;
    window.localStorage.setItem('admin_per_page', per_page);
    actions.pagination.setState({
      page: 1,
      per_page: parseInt(per_page, 10),
    });

    loadData();
  },

  /**
   * 切换页码
   */
  onPageChange: ({ e, loadData }) => (state, actions) => {
    e.preventDefault();

    const page = $(e.target).find('input[name="page"]').val();
    actions.pagination.setState({
      page: parseInt(page, 10),
    });

    loadData();
  },

  /**
   * 切换到上一页
   */
  toPrevPage: loadData => (state, actions) => {
    actions.pagination.setState({
      page: state.pagination.page - 1,
    });

    loadData();
  },

  /**
   * 切换到下一页
   */
  toNextPage: loadData => (state, actions) => {
    actions.pagination.setState({
      page: state.pagination.page + 1,
    });

    loadData();
  },
});
