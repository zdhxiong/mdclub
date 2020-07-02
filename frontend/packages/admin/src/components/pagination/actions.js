import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import commonActions from '~/utils/actionsAbstract';

const as = {
  /**
   * 销毁前执行
   */
  onDestroy: () => (state, actions) => {
    actions.reset();
  },

  /**
   * 重置状态
   */
  reset: () => (state, actions) => {
    // per_page 状态需要保留，不重置
    actions.setState({
      page: 1,
      previous: null,
      next: null,
      total: 0,
      pages: 0,
    });
  },

  /**
   * 修改每页行数
   */
  onPerPageChange: ({ e, per_page, onChange }) => (state, actions) => {
    e.preventDefault();

    window.localStorage.setItem('admin_per_page', per_page);

    actions.setState({ page: 1, per_page });

    $('#pagination-setting-menu-trigger').data('menu-instance').close();

    onChange();
  },

  /**
   * 切换到上一页
   */
  toPrevPage: (onChange) => (state, actions) => {
    actions.setState({ page: state.page - 1 });

    onChange();
  },

  /**
   * 切换到下一页
   */
  toNextPage: (onChange) => (state, actions) => {
    actions.setState({ page: state.page + 1 });

    onChange();
  },
};

export default extend(as, commonActions);
