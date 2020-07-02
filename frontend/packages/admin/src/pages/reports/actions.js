import mdui from 'mdui';
import R from 'ramda';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import {
  getList as getReports,
  del as deleteReport,
  deleteMultiple as deleteMultipleReports,
} from 'mdclub-sdk-js/es/ReportApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import { loadStart } from '~/utils/loading';

/**
 * 为举报信息添加额外字段
 */
const getExtraFields = (report) => {
  report.key = `${report.reportable_type}:${report.reportable_id}`;

  const map = {
    question: () => `提问：${report.relationships.question.title}`,
    article: () => `文章：${report.relationships.article.title}`,
    answer: () => `回答：${report.relationships.answer.content_summary}`,
    comment: () => `评论：${report.relationships.comment.content_summary}`,
    user: () => `用户：${report.relationships.user.username}`,
  };

  report.reportable_title = map[report.reportable_type]();

  return report;
};

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('举报管理');

    emit('searchbar_state_update', {
      fields: [
        {
          name: 'reportable_type',
          label: '类型',
          enum: [
            { name: '全部', value: '' },
            { name: '文章', value: 'article' },
            { name: '提问', value: 'question' },
            { name: '回答', value: 'answer' },
            { name: '评论', value: 'comment' },
            { name: '用户', value: 'user' },
          ],
        },
      ],
      data: {
        reportable_type: '',
      },
      isDataEmpty: true,
      isNeedRender: true,
    });

    emit('datatable_state_update', {
      columns: [
        {
          title: '内容',
          field: 'reportable_title',
          type: 'relation',
          onClick: ({ e, row }) => {
            e.preventDefault();

            switch (row.reportable_type) {
              case 'question':
                emit('question_open', row.relationships.question.question_id);
                break;
              case 'article':
                emit('article_open', row.relationships.article.article_id);
                break;
              case 'answer':
                emit('answer_open', row.relationships.answer.answer_id);
                break;
              case 'comment':
                emit('comment_open', row.relationships.comment.comment_id);
                break;
              case 'user':
                emit('user_open', row.relationships.user.user_id);
                break;
              default:
                break;
            }
          },
        },
        {
          title: '举报人数',
          field: 'reporter_count',
          type: 'handler',
          handler: (row) => `${row.reporter_count} 人举报`,
          width: 154,
        },
      ],
      buttons: [
        {
          type: 'target',
          getTargetLink: (row) => {
            switch (row.reportable_type) {
              case 'question':
                return `${window.G_ROOT}/questions/${row.reportable_id}`;
              case 'article':
                return `${window.G_ROOT}/articles/${row.reportable_id}`;
              case 'answer':
                return `${window.G_ROOT}/questions/${row.relationships.answer.question_id}/answers/${row.relationships.answer.answer_id}`;
              case 'comment':
                return '';
              case 'user':
                return `${window.G_ROOT}/users/${row.reportable_id}`;
              default:
                return '';
            }
          },
        },
        {
          type: 'btn',
          onClick: actions.deleteOne,
          label: '处理完成',
          icon: 'done',
        },
      ],
      batchButtons: [
        {
          label: '批量处理完成',
          icon: 'done_all',
          onClick: actions.batchDelete,
        },
      ],
      primaryKey: 'key',
      onRowClick: (report) => {
        emit('reporters_open', report);
        emit('datatable_state_update', {
          lastVisitId: report.key,
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

    const searchData = R.pipe(
      R.filter((n) => n),
      R.mergeDeepLeft({ page, per_page }),
    )(searchBar.getState().data);

    searchData.include = ['question', 'answer', 'article', 'comment', 'user'];

    datatable.loadStart();

    getReports(searchData)
      .then((response) => {
        response.data.map((item, index) => {
          response.data[index] = getExtraFields(item);
        });

        datatable.loadSuccess(response);
      })
      .catch(datatable.loadFail);
  },

  /**
   * 删除一个举报
   */
  deleteOne: ({ reportable_type, reportable_id }) => (state, actions) => {
    const confirm = () => {
      loadStart();

      deleteReport({ reportable_type, reportable_id })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    const options = { confirmText: '确认', cancelText: '取消' };

    mdui.confirm('确认已处理完该举报？', confirm, () => {}, options);
  },

  /**
   * 批量删除举报
   */
  batchDelete: (reports) => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loadStart();

      const report_targets = R.pipe(
        R.map((n) => `${n.reportable_type}:${n.reportable_id}`),
        R.join(','),
      )(reports);

      deleteMultipleReports({ report_targets })
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(
      `确认已处理完这 ${reports.length} 个举报？`,
      confirm,
      () => {},
      options,
    );
  },
};

export default extend(as, commonActions);
