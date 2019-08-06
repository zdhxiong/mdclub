import mdui, { JQ as $ } from 'mdui';
import R from 'ramda';
import { location } from '@hyperapp/router';
import { Report } from 'mdclub-sdk-js';
import loading from '../../helper/loading';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

/**
 * 为举报信息添加额外字段
 */
const getExtraFields = (report) => {
  report.key = `${report.reportable_type}:${report.reportable_id}`;

  const map = {
    question: () => `提问：${report.relationship.question.title}`,
    article: () => `文章：${report.relationship.article.title}`,
    answer: () => `回答：${report.relationship.answer.content_summary}`,
    comment: () => `评论：${report.relationship.comment.content_summary}`,
    user: () => `用户：${report.relationship.user.username}`,
  };

  report.reportable_title = map[report.reportable_type]();

  return report;
};

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange('举报管理 - MDClub 控制台');
    global_actions = props.global_actions;

    const { user, reporters, searchBar, datatable } = global_actions.components;

    searchBar.setState({
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

    datatable.setState({
      columns: [
        {
          title: '内容',
          field: 'reportable_title',
          type: 'relation',
          onClick: ({ e, row }) => {
            e.preventDefault();

            switch (row.reportable_type) {
              case 'question':
                break;
              case 'article':
                break;
              case 'answer':
                break;
              case 'comment':
                break;
              case 'user':
                user.open(row.relationship.user.user_id);
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
          handler: row => `${row.reporter_count} 人举报`,
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
                return '';
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
      onRowClick: reporters.open,
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

    const searchData = R.pipe(
      R.filter(n => n),
      R.mergeDeepLeft({ page, per_page }),
    )(searchBar.getState().data);

    datatable.loadStart();

    Report
      .getList(searchData)
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
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      Report
        .deleteOne(reportable_type, reportable_id)
        .then(actions.deleteSuccess)
        .then(actions.deleteFail);
    };

    mdui.confirm('确认已处理完该举报？', confirm, false, options);
  },

  /**
   * 批量删除举报
   */
  batchDelete: reports => (state, actions) => {
    const options = { confirmText: '确认', cancelText: '取消' };

    const confirm = () => {
      loading.start();

      const targets = R.pipe(
        R.map(n => `${n.reportable_type}:${n.reportable_id}`),
        R.join(','),
      )(reports);

      Report
        .deleteMultiple(targets)
        .then(actions.deleteSuccess)
        .catch(actions.deleteFail);
    };

    mdui.confirm(`确认已处理完这 ${reports.length} 个举报？`, confirm, false, options);
  },
});
