import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Topic } from 'mdclub-sdk-js';

let global_actions;

export default {
  setState: value => (value),
  getState: () => state => state,

  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.routeChange();

    if (state.pagination) {
      return;
    }

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
};
