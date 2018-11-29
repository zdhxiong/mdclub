import mdui, { JQ as $ } from 'mdui';
import { location } from '@hyperapp/router';
import { Report } from 'mdclub-sdk-js';
import actionsAbstract from '../../abstracts/pageActions';

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

export default $.extend({}, actionsAbstract, {
  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    actions.routeChange();
    actions.loadData();
  },

  /**
   * 加载数据
   */
  loadData: () => (state, actions) => {
    const datatableActions = global_actions.lazyComponents.datatable;
    datatableActions.loadStart();

    const datatableState = datatableActions.getState();

    Report.getList({
      page: datatableState.pagination.page,
      per_page: datatableState.pagination.per_page,
    }, (response) => {
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
                global_actions.lazyComponents.userDialog.open(row.relationship.user.user_id);
                break;
              default:
                break;
            }
          }
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
      response.onRowClick = global_actions.lazyComponents.reportersDialog.open;
      datatableActions.loadEnd(response);
    });
  },

  /**
   * 删除一个举报
   */
  deleteOne: report => (state, actions) => {

  },

  /**
   * 批量删除举报
   */
  batchDelete: reports => (state, actions) => {

  },
});
