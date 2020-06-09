import $ from 'mdui.jq';
import { subscribe } from '~/utils/pubsub';

/**
 * 全局事件监听
 * 需要在子组件中操作父组件时，只能通过触发这里指定的事件来实现
 * @param app
 */
function listener(app) {
  // 更新当前登录用户信息
  subscribe('user_update', (user) => {
    app.user.setState({ user });
  });

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
      const drawerInstance = $('.mc-drawer').data('drawerInstance');

      if (drawerInstance) {
        drawerInstance.close();
      }
    }

    app.login.close();
    app.register.close();
    app.reset.close();
    app.usersDialog.close();
    app.comments.closeDialog();
    app.reportDialog.close();
    app.shareDialog.close();
  });

  // 打开登录对话框
  subscribe('login_open', () => {
    app.login.open();
  });

  // 打开注册对话框
  subscribe('register_open', () => {
    app.register.open();
  });

  // 打开重置密码对话框
  subscribe('reset_open', () => {
    app.reset.open();
  });

  // 打开用户列表对话框
  subscribe('users_dialog_open', (props) => {
    app.usersDialog.open(props);
  });

  /**
   * 打开分享对话框
   */
  subscribe('share_dialog_open', (props) => {
    app.shareDialog.open(props);
  });

  /**
   * 打开举报对话框
   */
  subscribe('report_dialog_open', (props) => {
    app.reportDialog.open(props);
  });

  // 关注或取消关注用户后，下次打开关注列表页面时，需要重新加载关注列表
  subscribe('users_follow_updated', () => {
    app.users.followUpdate();
  });

  // 关注或取消关注话题后，下次打开关注列表页面时，需要重新加载关注列表
  subscribe('topics_follow_updated', () => {
    app.topics.followUpdate();
  });

  // 关注或取消关注提问后，下次打开关注列表页面时，需要重新加载关注列表
  subscribe('questions_follow_updated', () => {
    app.questions.followUpdate();
  });

  // 关注或取消关注文章后，下次打开关注列表页面时，需要重新加载关注列表
  subscribe('articles_follow_updated', () => {
    app.articles.followUpdate();
  });

  // 更新提问详情后，需要更新列表中对应的提问
  subscribe('question_updated', (question) => {
    app.questions.questionUpdate(question);
  });

  // 更新未读通知数量
  subscribe('notification_count_update', (count) => {
    app.notifications.updateCount(count);
  });

  // 点击评论按钮，打开评论模态框
  subscribe('comments_dialog_open', (props) => {
    app.comments.openDialog(props);
  });
}

export default listener;
