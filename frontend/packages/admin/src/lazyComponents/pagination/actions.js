import { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/actions';

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    $(props.element).mutation();

    if (!state.per_page) {
      const per_page = window.localStorage.getItem('per_page');

      actions.setState({
        per_page: per_page ? parseInt(per_page, 10) : 10,
      });
    }
  },

  /**
   * 重置分页参数
   */
  reset: () => (state, actions) => {
    actions.setState({
      page: 1,
      previous: null, // 上一页页码
      next: null, // 下一页页码
      total: 0, // 总行数
      pages: 0, // 总页数
      loading: false, // 是否正在加载中
    });
  },

  /**
   * 修改每页行数
   */
  onPerPageChange: ({ e, onPaginationChange }) => (state, actions) => {
    const per_page = e.target.value;
    window.localStorage.setItem('per_page', per_page);
    actions.setState({
      page: 1,
      per_page: parseInt(per_page, 10),
    });

    onPaginationChange();
  },

  /**
   * 切换页码
   */
  onPageChange: ({ e, onPaginationChange }) => (state, actions) => {
    e.preventDefault();

    const page = $(e.target).find('input[name="page"]').val();
    actions.setState({
      page: parseInt(page, 10),
    });

    onPaginationChange();
  },

  /**
   * 切换到上一页
   */
  toPrevPage: onPaginationChange => (state, actions) => {
    actions.setState({
      page: state.page - 1,
    });

    onPaginationChange();
  },

  /**
   * 切换到下一页
   */
  toNextPage: onPaginationChange => (state, actions) => {
    actions.setState({
      page: state.page + 1,
    });

    onPaginationChange();
  },
});
