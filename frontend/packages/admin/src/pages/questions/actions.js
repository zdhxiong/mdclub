import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getQuestions,
  del as deleteQuestion,
  deleteMultiple as deleteMultipleQuestions,
} from 'mdclub-sdk-js/es/QuestionApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('提问管理');

    emit('searchbar_state_update', {
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

    emit('datatable_state_update', {
      columns: [
        {
          title: '作者',
          field: 'relationships.user.username',
          type: 'relation',
          width: 160,
          onClick: ({ e, row }) => {
            e.preventDefault();
            emit('user_open', row.user_id);
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
          getTargetLink: ({ question_id }) => {
            return `${window.G_ROOT}/questions/${question_id}`;
          },
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
      onRowClick: (question) => {
        emit('question_open', question);
        emit('datatable_state_update', {
          lastVisitId: question.question_id,
        });
      },
    });

    $document.on('search-submit', actions.loadData);
    actions.loadData();
  },

  onDestroy: () => {
    $document.off('search-submit');
  },

  /**
   * 加载数据
   */
  loadData: () => {
    const { datatable, pagination, searchBar } = window.app;
    const { page, per_page } = pagination.getState();
    const { order } = datatable.getState();

    const searchData = R.pipe(
      R.filter((n) => n),
      R.mergeDeepLeft({ page, per_page, order }),
    )(searchBar.getState().data);

    searchData.include = ['user', 'topics'];

    datatable.loadStart();

    getQuestions(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定提问
   */
  editOne: (question) => {
    emit('question_edit_open', question);
  },

  /**
   * 删除指定提问
   */
  deleteOne: ({ question_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      deleteQuestion({ question_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认删除该提问？', confirm, () => {}, options);
  },

  /**
   * 批量删除提问
   */
  batchDelete: (questions) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const question_ids = R.pipe(
        R.pluck('question_id'),
        R.join(','),
      )(questions);

      deleteMultipleQuestions({ question_ids })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认删除这 ${questions.length} 个提问？`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
