import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Question } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/actions';

let global_actions;

let columns = [
  {
    title: 'ID',
  },
  {
    title: '作者',
  },
  {
    title: '标题',
  },
  {
    title: '发表时间',
  },
  {
    title: '',
  },
];

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    actions.routeChange();
    actions.loadData();
  },

  dataFormat: (data) => {
    return data;
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;
    const datatableState = datatableActions.getState();

    datatableActions.loadStart();

    Question.getList({
      page: datatableState.pagination.page,
      per_page: datatableState.pagination.per_page,
      order: datatableState.order,
    }, (response) => {
      datatableActions.loadEnd(response);
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
