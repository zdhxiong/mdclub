import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Question } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    actions.routeChange();
    actions.loadData();
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const paginationActions = global_actions.lazyComponents.pagination;
    const paginationState = paginationActions.getState();

    actions.setState({ data: [] });
    paginationActions.setState({ loading: true });

    Question.getList({
      page: paginationState.page,
      per_page: paginationState.per_page,
      order: state.order,
    }, (response) => {
      paginationActions.setState({ loading: false });

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({ data: response.data });
      paginationActions.setState(response.pagination);
    });
  },

  /**
   * 删除指定提问
   */
  deleteOne: question_id => (state, actions) => {
    mdui.confirm('删除后，你仍可以在回收站中恢复该提问', '确定删除该提问？', () => {
      Question.deleteOne(question_id, (response) => {
        if (response.code) {
          mdui.snackbar(response.message);
          return;
        }

        actions.loadData();
      });
    });
  },
});
