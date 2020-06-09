import mdui from 'mdui/es/mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import {
  getList as getNotifications,
  del as deleteNotification,
  readAll,
} from 'mdclub-sdk-js/es/NotificationApi';
import { get as getAnswer } from 'mdclub-sdk-js/es/AnswerApi';
import { get as getComment } from 'mdclub-sdk-js/es/CommentApi';
import commonActions from '~/utils/actionsAbstract';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import currentUser from '~/utils/currentUser';
import { findIndex } from '~/utils/func';

const per_page = 20;
const include = [
  // 'receiver',
  'sender',
  'article',
  'question',
  'answer',
  'comment',
  'reply',
];

// 下次打开通知页面是否需要重新加载
const needReload = false;

const transformData = (data) => {
  return data.map((item) => {
    // 是否已加载通知对应的详细信息
    item.is_loaded_detail = false;

    // 是否显示通知对应的详细内容
    item.is_show_detail = false;

    // 通知对应的详细信息内容
    item.content_detail = '';

    return item;
  });
};

const as = {
  onCreate: () => (state, actions) => {
    emit('route_update');
    actions.setTitle('通知');

    if (!currentUser()) {
      return;
    }

    // 当有未读通知时，需要重新加载列表
    if (!state.pagination || state.count || needReload) {
      actions.loadData();
    }

    // 进入通知页面，即标记所有通知为已读
    readAll()
      .then(() => {
        actions.updateCount(0);
      })
      .catch(apiCatch);

    $window.on('scroll', actions.infiniteLoad);
  },

  onDestroy: () => (_, actions) => {
    $window.off('scroll', actions.infiniteLoad);
  },

  /**
   * 更新未读通知数量
   */
  updateCount: (count) => (_, actions) => {
    actions.setState({ count });
  },

  /**
   * 加载通知列表
   */
  loadData: () => (state, actions) => {
    actions.setState({
      data: [],
      pagination: null,
      loading: true,
    });

    getNotifications({ page: 1, per_page, include })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then((response) => {
        actions.setState({
          data: transformData(response.data),
          pagination: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 绑定下拉加载更多
   */
  infiniteLoad: () => (state, actions) => {
    if (state.loading) {
      return;
    }

    const { pagination } = state;

    if (pagination.page >= pagination.pages) {
      return;
    }

    if (
      document.body.scrollHeight - window.pageYOffset - window.innerHeight >
      100
    ) {
      return;
    }

    actions.setState({ loading: true });

    getNotifications({
      page: pagination.page + 1,
      per_page,
      include,
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then((response) => {
        actions.setState({
          data: state.data.concat(transformData(response.data)),
          pagination: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 删除一条通知
   */
  deleteOne: (notification) => (state, actions) => {
    const onConfirm = () => {
      const { data } = state;
      const index = findIndex(
        data,
        'notification_id',
        notification.notification_id,
      );

      data.splice(index, 1);

      actions.setState({ data });

      deleteNotification({
        notification_id: notification.notification_id,
      }).catch(apiCatch);
    };
    const onCancel = () => false;
    const options = {
      history: false,
      confirmText: '确定',
      cancelText: '取消',
    };

    mdui.confirm('确认删除这条通知？', onConfirm, onCancel, options);
  },

  /**
   * 切换通知详情的显示
   */
  toggleDetail: (notification) => (state, actions) => {
    const { data } = state;
    const index = findIndex(
      data,
      'notification_id',
      notification.notification_id,
    );
    const item = data[index];

    item.is_show_detail = !item.is_show_detail;

    if (item.is_show_detail && item.is_loaded_detail) {
      // 已经加载过详情，直接切换
      data[index] = item;
      actions.setState({ data });
    } else {
      // 加载详情
      switch (item.type) {
        // content_deleted 中加载文章、提问、回答、评论
        case 'article_deleted':
        case 'question_deleted':
        case 'answer_deleted':
        case 'comment_deleted':
          item.is_loaded_detail = true;
          item.content_detail =
            item.type === 'comment_deleted'
              ? item.content_deleted.content
              : item.content_deleted.content_rendered;
          data[index] = item;
          actions.setState({ data });
          break;

        // ajax 加载评论、回答
        case 'question_commented':
        case 'article_commented':
        case 'answer_commented':
        case 'comment_replies':
        case 'question_answered':
          data[index] = item;
          actions.setState({ data });

          (item.type === 'question_answered'
            ? getAnswer({ answer_id: item.answer_id })
            : getComment({ comment_id: item.comment_id })
          )
            .then((response) => {
              item.is_loaded_detail = true;
              item.content_detail =
                item.type === 'question_answered'
                  ? response.data.content_rendered
                  : response.data.content;
              data[index] = item;
              actions.setState({ data });
            })
            .catch(apiCatch);
          break;

        default:
          break;
      }
    }
  },
};

export default extend(as, commonActions);
