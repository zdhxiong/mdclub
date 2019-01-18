import { JQ as $ } from 'mdui';
import actionsAbstract from '../../abstracts/actions/component';

export default $.extend({}, actionsAbstract, {
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
  onPerPageChange: ({ e, num, onChange }) => (state, actions) => {
    e.preventDefault();

    const per_page = num;
    window.localStorage.setItem('admin_per_page', per_page);
    actions.setState({
      page: 1,
      per_page: parseInt(per_page, 10),
    });

    onChange();
  },

  /**
   * 切换页码
   */
  onPageChange: ({ e, onChange }) => (state, actions) => {
    e.preventDefault();

    const page = $(e.target).find('input[name="page"]').val();
    actions.setState({
      page: parseInt(page, 10),
    });

    onChange();
  },

  /**
   * 切换到上一页
   */
  toPrevPage: onChange => (state, actions) => {
    actions.setState({
      page: state.page - 1,
    });

    onChange();
  },

  /**
   * 切换到下一页
   */
  toNextPage: onChange => (state, actions) => {
    actions.setState({
      page: state.page + 1,
    });

    onChange();
  },
});
