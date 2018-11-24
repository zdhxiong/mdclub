import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Topic } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    actions.routeChange();

    actions.setState({
      data: [],
      pagination: false,
      loading: true,
    });

    Topic.getList({
      order: 'topic_id',
    }, (response) => {
      actions.setState({ loading: false });

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        data: response.data,
        pagination: response.pagination,
      });
    });
  },
});
