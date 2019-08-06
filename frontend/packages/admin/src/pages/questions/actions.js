import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import { location } from '@hyperapp/router';
import { Question } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange('提问管理 - MDClub 控制台');
    global_actions = props.global_actions;

    const { user, question, searchBar, datatable } = global_actions.components;

    searchBar.setState({
      fields: [
        { name: 'question_id', label: '提问ID' },
        { name: 'user_id', label: '用户ID' },
      ],
      data: {
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
          title: '标题',
          field: 'title',
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
          getTargetLink: ({ question_id }) => `${window.G_ROOT}/questions/${question_id}`,
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
      primaryKey: 'question_id',
      onRowClick: question.open,
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

    Question
      .getList(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定提问
   */
  editOne: question => (state, actions) => {

  },

  /**
   * 删除指定提问
   */
  deleteOne: ({ question_id }) => (state, actions) => {
    const confirm = () => {
      loading.start();

      Question
        .deleteOne(question_id)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('删除后，你仍可以在回收站中恢复该提问', '确认删除该提问？', confirm, false, options);
  },

  /**
   * 批量删除提问
   */
  batchDelete: questions => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      const question_ids = R.pipe(
        R.pluck('question_id'),
        R.join(','),
      )(questions);

      Question
        .deleteMultiple(question_ids)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm('删除后，你仍可以在回收站中恢复这些提问', `确认删除这 ${questions.length} 个提问？`, confirm, false, options);
  },
});
