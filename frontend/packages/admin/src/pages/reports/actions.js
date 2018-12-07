import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Report } from 'mdclub-sdk-js';
import ObjectHelper from '../../helper/obj';
import actionsAbstract from '../../abstracts/actions/page';

let global_actions;

/**
 * 为举报信息添加额外字段
 */
const getExtraFields = (report) => {
  report.key = `${report.reportable_type}:${report.reportable_id}`;

  switch (report.reportable_type) {
    case 'question':
      report.reportable_title = `提问：${report.relationship.question.title}`;
      break;
    case 'article':
      report.reportable_title = `文章：${report.relationship.article.title}`;
      break;
    case 'answer':
      report.reportable_title = `回答：${report.relationship.answer.content_summary}`;
      break;
    case 'comment':
      report.reportable_title = `评论：${report.relationship.comment.content_summary}`;
      break;
    case 'user':
      report.reportable_title = `用户：${report.relationship.user.username}`;
      break;
    default:
      break;
  }

  return report;
};

const searchState = {
  fields: [
    {
      name: 'reportable_type',
      label: '类型',
      enum: [
        {
          name: '全部',
          value: '',
        },
        {
          name: '文章',
          value: 'article',
        },
        {
          name: '提问',
          value: 'question',
        },
        {
          name: '回答',
          value: 'answer',
        },
        {
          name: '评论',
          value: 'comment',
        },
        {
          name: '用户',
          value: 'user',
        },
      ],
    },
  ],
  data: {
    reportable_type: '',
  },
  isDataEmpty: true,
};

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    actions.routeChange();
    global_actions = props.global_actions;
    global_actions.lazyComponents.searchBar.setState(searchState);

    $(document).on('search-submit', () => {
      actions.loadData();
    });

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
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;

    datatableActions.loadStart();

    const paginationState = global_actions.lazyComponents.pagination.getState();
    const searchBarData = global_actions.lazyComponents.searchBar.getState().data;

    const data = $.extend({}, ObjectHelper.filter(searchBarData), {
      page: paginationState.page,
      per_page: paginationState.per_page,
    });

    const success = (response) => {
      const columns = [
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
                global_actions.lazyComponents.dialogUser.open(row.relationship.user.user_id);
                break;
              default:
                break;
            }
          },
        },
        {
          title: '举报人数',
          field: 'reporter_count',
          type: 'number',
        },
      ];

      const _actions = [
        {
          type: 'btn',
          onClick: actions.deleteOne,
          label: '处理完成',
          icon: 'done',
        },
      ];

      const batchActions = [
        {
          label: '批量处理完成',
          icon: 'done_all',
          onClick: actions.batchDelete,
        },
      ];

      response.data.map((item, index) => {
        response.data[index] = getExtraFields(item);
      });

      response.primaryKey = 'key';
      response.columns = columns;
      response.actions = _actions;
      response.batchActions = batchActions;
      response.onRowClick = global_actions.lazyComponents.dialogReporters.open;
      datatableActions.loadEnd(response);
    };

    Report.getList(data, success);
  },

  /**
   * 删除一个举报
   */
  deleteOne: ({ reportable_type, reportable_id }) => (state, actions) => {
    const confirm = () => {
      $.loadStart();
      Report.deleteOne(reportable_type, reportable_id, actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm('确认已处理完该举报？', confirm, false, options);
  },

  /**
   * 批量删除举报
   */
  batchDelete: reports => (state, actions) => {
    const confirm = () => {
      $.loadStart();

      const targets = [];
      reports.map(({ reportable_type, reportable_id }) => {
        targets.push(`${reportable_type}:${reportable_id}`);
      });

      Report.deleteMultiple(targets.join(','), actions.deleteSuccess);
    };

    const options = {
      confirmText: '确认',
      cancelText: '取消',
    };

    mdui.confirm(`确认已处理完这 ${reports.length} 个举报？`, confirm, false, options);
  },
});
