import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getAnswers,
  del as deleteAnswer,
  deleteMultiple as deleteMultipleAnswers,
} from 'mdclub-sdk-js/es/AnswerApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('回答管理');

    emit('searchbar_state_update', {
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
          title: '回答',
          field: 'content_markdown',
          type: 'string',
        },
        {
          title: '创建时间',
          field: 'create_time',
          type: 'time',
          width: 186,
        },
      ],
      buttons: [
        {
          type: 'target',
          getTargetLink: ({
            answer_id,
            relationships: {
              question: { question_id },
            },
          }) => {
            return `${window.G_ROOT}/questions/${question_id}/answers/${answer_id}`;
          },
        },
        {
          type: 'btn',
          onClick: actions.viewQuestion,
          label: '查看所属提问',
          icon: 'question_answer',
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
      onRowClick: (answer) => {
        emit('answer_open', answer);
        emit('datatable_state_update', {
          lastVisitId: answer.answer_id,
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

    searchData.include = ['user', 'question'];

    datatable.loadStart();

    getAnswers(searchData)
      .then(datatable.loadSuccess)
      .catch(datatable.loadFail);
  },

  /**
   * 编辑指定回答
   */
  editOne: (answer) => {
    emit('answer_edit_open', answer);
  },

  /**
   * 查看所属提问
   */
  viewQuestion: (answer) => {
    emit('question_open', answer.question_id);
  },

  /**
   * 删除指定回答
   */
  deleteOne: ({ answer_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      deleteAnswer({ answer_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认删除该回答？', confirm, () => {}, options);
  },

  /**
   * 批量删除回答
   */
  batchDelete: (answers) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const answer_ids = R.pipe(R.pluck('answer_id'), R.join(','))(answers);

      deleteMultipleAnswers({ answer_ids })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认删除这 ${answers.length} 个回答？`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
