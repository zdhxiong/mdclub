import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import { location } from '@hyperapp/router';
import { Answer } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange('回答管理 - MDClub 控制台');
    global_actions = props.global_actions;

    const { user, answer, searchBar, datatable } = global_actions.components;

    searchBar.setState({
      fields: [
        { name: 'answer_id', label: '回答ID' },
        { name: 'question_id', label: '提问ID' },
        { name: 'user_id', label: '用户ID' },
      ],
      data: {
        answer_id: '',
        question_id: '',
        user_id: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    });

    datatable.setState({
      columns: [
        {
          title: '作者',
          field: 'relationship.user.username',
          type: 'relation',
          width: 160,
          onClick: ({ e, row }) => {
            e.preventDefault();
            user.open(row.user_id);
          },
        },
        {
          title: '回答',
          field: 'content_markdown',
          type: 'string',
        },
        {
          title: '创建时间',
          field: 'create_time',
          type: 'time',
          width: 154,
        },
      ],
      buttons: [
        {
          type: 'target',
          getTargetLink: ({ relationship: { question: { question_id } } }) => `${window.G_ROOT}/questions/${question_id}`,
        },
        {
          type: 'btn',
          onClick: actions.editOne,
          label: '编辑',
          icon: 'edit',
        },
        {
          type: 'btn',
          onClick: actions.deleteOne,
          label: '删除',
          icon: 'delete',
        },
      ],
      batchButtons: [
        {
          label: '批量删除',
          icon: 'delete',
          onClick: actions.batchDelete,
        },
      ],
      orders: [
        { name: '创建时间', value: '-create_time' },
        { name: '上次更新时间', value: '-update_time' },
        { name: '投票数', value: '-vote_count' },
      ],
      order: '-create_time',
      primaryKey: 'answer_id',
      onRowClick: answer.open,
    });

    $(document).on('search-submit', actions.loadData);
    actions.loadData();
  },

  /**
   * 销毁前
   */
  destroy: () => {
    $(document).off('search-submit');
  },

  /**
   * 加载数据
   */
  loadData: () => {
    const { datatable, pagination, searchBar } = global_actions.components;
    const { page, per_page } = pagination.getState();
    const { order } = datatable.getState();

    const searchData = R.pipe(
      R.filter(n => n),
      R.mergeDeepLeft({ page, per_page, order }),
    )(searchBar.getState().data);

    datatable.loadStart();

    Answer
      .getList(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定回答
   */
  editOne: answer => (state, actions) => {

  },

  /**
   * 删除指定回答
   */
  deleteOne: ({ answer_id }) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      Answer
        .deleteOne(answer_id)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复该回答', '确认删除该回答', confirm, false, options);
  },

  /**
   * 批量删除回答
   */
  batchDelete: answers => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      const answer_ids = R.pipe(
        R.pluck('answer_id'),
        R.join(','),
      )(answers);

      Answer
        .deleteMultiple(answer_ids)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些回答', `确认删除这 ${answers.length} 个回答`, confirm, false, options);
  },
});
