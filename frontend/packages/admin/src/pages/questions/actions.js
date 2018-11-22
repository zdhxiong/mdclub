import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Question } from 'mdclub-sdk-js';

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
    mdui.mutation();

    if (state.pagination) {
      return;
    }

    actions.setState({
      data: [],
      pagination: false,
      loading: true,
    });

    Question.getList({
      order: '-create_time',
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

  update: props => (state, actions) => {
    const element = props.element;

    mdui.updateTables(element);
  },
};
