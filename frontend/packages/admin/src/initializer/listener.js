import $ from 'mdui.jq';
import { subscribe } from '~/utils/pubsub';

/**
 * 全局事件监听
 * 需要在子组件中操作父组件时，只能通过触发这里指定的事件来实现
 * @param app
 */
function listener(app) {
  // 设置布局色
  subscribe('layout_update', (layout) => {
    app.theme.setLayout(layout);
  });

  // 更新路由事件
  subscribe('route_update', () => {
    // 回到页面顶部
    window.scrollTo(0, 0);

    // 移除 tooltip 元素
    $('.mdui-tooltip').remove();

    // 在手机和平板时，切换路由后关闭抽屉导航
    if (window.innerWidth < 1024) {
      const drawerInstance = $('.mc-drawer').data('drawer-instance');

      if (drawerInstance) {
        drawerInstance.close();
      }
    }
  });

  // 更新搜索栏的状态
  subscribe('searchbar_state_update', (state) => {
    app.searchBar.setState(state);
  });

  // 更新应用栏状态
  subscribe('appbar_state_update', (state) => {
    app.appbar.setState(state);
  });

  // 更新分页状态
  subscribe('pagination_state_update', (state) => {
    app.pagination.setState(state);
  });

  // 更新数据表格状态
  subscribe('datatable_state_update', (state) => {
    app.datatable.setState(state);
  });

  // 更新数据表格中的一行数据
  subscribe('datatable_update_row', (row) => {
    app.datatable.updateOne(row);
  });

  // 打开话题详情对话框
  subscribe('topic_open', (topic) => {
    app.topic.open(topic);
  });

  // 打开话题编辑对话框
  subscribe('topic_edit_open', (topic) => {
    app.topicEdit.open(topic);
  });

  // 打开用户详情对话框
  subscribe('user_open', (user) => {
    app.user.open(user);
  });

  // 打开用户编辑对话框
  subscribe('user_edit_open', (user) => {
    app.userEdit.open(user);
  });

  // 打开提问详情对话框
  subscribe('question_open', (question) => {
    app.question.open(question);
  });

  // 打开提问编辑对话框
  subscribe('question_edit_open', (question) => {
    app.questionEdit.editorOpen(question);
  });

  // 打开回答详情对话框
  subscribe('answer_open', (answer) => {
    app.answer.open(answer);
  });

  // 打开回答编辑对话框
  subscribe('answer_edit_open', (answer) => {
    app.answerEdit.editorOpen(answer);
  });

  // 打开文章详情对话框
  subscribe('article_open', (article) => {
    app.article.open(article);
  });

  // 打开文章编辑对话框
  subscribe('article_edit_open', (article) => {
    app.articleEdit.editorOpen(article);
  });

  // 打开评论详情对话框
  subscribe('comment_open', (comment) => {
    app.comment.open(comment);
  });

  // 打开评论编辑对话框
  subscribe('comment_edit_open', (comment) => {
    app.commentEdit.open(comment);
  });

  // 打开举报用户列表对话框
  subscribe('reporters_open', (report) => {
    app.reporters.open(report);
  });
}

export default listener;
