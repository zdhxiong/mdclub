import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import { $window, $document } from 'mdui/es/utils/dom';
import {
  createComment as createQuestionComment,
  getComments as getQuestionComments,
} from 'mdclub-sdk-js/es/QuestionApi';
import {
  createComment as createAnswerComment,
  getComments as getAnswerComments,
} from 'mdclub-sdk-js/es/AnswerApi';
import {
  createComment as createArticleComment,
  getComments as getArticleComments,
} from 'mdclub-sdk-js/es/ArticleApi';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import { createReply, getReplies } from 'mdclub-sdk-js/es/CommentApi';
import { isUndefined } from 'mdui.jq/es/utils';
import commonActions from '~/utils/actionsAbstract';
import apiCatch from '~/utils/errorHandler';
import { findIndex } from '~/utils/func';
import voteActions from '~/components/vote/actions';
import userPopoverActions from '~/components/user-popover/actions';
import stateDefault from './stateDefault';

const per_page = 20;
const include = ['user', 'voting'];
let $commentsWrapper;

// 获取评论列表
const getComments = (commentable_type, commentable_id, order, page = 1) => {
  const params = {
    page,
    per_page,
    order,
    include,
  };

  let promise;

  switch (commentable_type) {
    case 'question':
      params.question_id = commentable_id;
      promise = getQuestionComments(params);
      break;
    case 'article':
      params.article_id = commentable_id;
      promise = getArticleComments(params);
      break;
    default:
      // answer
      params.answer_id = commentable_id;
      promise = getAnswerComments(params);
      break;
  }

  promise.then((response) => {
    response.data.forEach((item, index) => {
      // 添加 show_replies 字段，表示是否显示评论的回复列表
      response.data[index].show_replies = false;

      // 添加 show_new_reply 字段，表示是否显示回复框
      response.data[index].show_new_reply = false;

      // 是否正在发表回复
      response.data[index].reply_submitting = false;

      // 是否正在加载回复列表
      response.data[index].replies_loading = false;

      // 回复列表数据列表
      response.data[index].replies_data = [];

      // 回复列表数据分页
      response.data[index].replies_pagination = null;
    });

    return response;
  });

  return promise;
};

/**
 * isInDialog: 是否在模态框中
 * 若不在模态框中，直接加载评论
 * 若在模态框中，在调用 openDialog 方法后加载评论
 */
const as = {
  onCreate: ({ commentable_type, commentable_id, isInDialog }) => (
    state,
    actions,
  ) => {
    const isTargetUpdated =
      state.commentable_type !== commentable_type ||
      state.commentable_id !== commentable_id;

    if (isTargetUpdated && !isInDialog) {
      actions.setState(stateDefault);
      actions.setState({ commentable_type, commentable_id });
      actions.loadData();
    }

    if (!isInDialog) {
      $window.on('scroll', actions.infiniteLoadInPage);
    }
  },

  onDestroy: ({ isInDialog }) => (_, actions) => {
    if (!isInDialog) {
      $window.off('scroll', actions.infiniteLoadInPage);
    }
  },

  /**
   * 指定关闭评论模态框
   */
  doCloseDialog: () => (_, actions) => {
    $.hideOverlay();
    setTimeout(() => {
      $('.mc-comments-dialog').hide();
      $.unlockScreen();
    }, 300);

    actions.setState({ open_dialog: false });
  },

  /**
   * 打开评论模态框
   */
  openDialog: ({ commentable_type, commentable_id }) => (state, actions) => {
    $commentsWrapper = $('.comments-wrapper');

    $.showOverlay();
    $.lockScreen();
    $('.mc-comments-dialog').show();
    $document.on('click', '.mdui-overlay', actions.doCloseDialog);

    $commentsWrapper.on('scroll', actions.infiniteLoadInDialog);

    const isTargetUpdated =
      state.commentable_type !== commentable_type ||
      state.commentable_id !== commentable_id;

    if (isTargetUpdated) {
      actions.setState(stateDefault);
      actions.setState({ commentable_type, commentable_id });
      actions.loadData();
    }

    actions.setState({ open_dialog: true });
  },

  /**
   * 关闭评论模态框
   */
  closeDialog: () => (state, actions) => {
    if (!state.open_dialog) {
      return;
    }

    actions.doCloseDialog();

    $document.off('click', '.mdui-overlay', actions.doCloseDialog);
    $commentsWrapper.off('scroll', actions.infiniteLoadInDialog);
  },

  /**
   * 发布评论
   */
  onSubmit: () => (state, actions) => {
    const funcMaps = {
      question: createQuestionComment,
      answer: createAnswerComment,
      article: createArticleComment,
    };
    const fieldMaps = {
      question: 'question_id',
      answer: 'answer_id',
      article: 'article_id',
    };

    const $newComment = $('.new-comment');
    const $textarea = $newComment.find('textarea');

    const content = $textarea.val();

    if (!content) {
      mdui.snackbar('请输入评论内容');
      return;
    }

    const params = {
      [fieldMaps[state.commentable_type]]: state.commentable_id,
      content,
      include: ['user', 'voting'],
    };

    actions.setState({ submitting: true });

    funcMaps[state.commentable_type](params)
      .finally(() => {
        actions.setState({ submitting: false });
      })
      .then(({ data }) => {
        mdui.snackbar('评论发布成功');

        $textarea.val('');
        mdui.updateTextFields($newComment);

        // 若按热门排序，且已加载到最后一页，把新评论插到最后面
        // 若按时间顺序，且已加载到最后一页，把新评论插到最后面
        // 若按时间倒序，把新评论插到最前面
        const { pagination } = state;
        const isLoadedAll =
          pagination &&
          (pagination.page === pagination.pages || !pagination.pages);

        pagination.total += 1;

        actions.setState({ pagination });

        if (state.order === '-create_time') {
          actions.setState({
            comments_data: [data].concat(state.comments_data),
          });
        } else if (isLoadedAll) {
          actions.setState({
            comments_data: state.comments_data.concat(data),
          });
        }
      })
      .catch((response) => {
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          mdui.snackbar(response.errors.content);
          return;
        }

        apiCatch(response);
      });
  },

  /**
   * 切换评论的排序方式
   */
  changeOrder: (order) => (state, actions) => {
    if (order === state.order) {
      return;
    }

    actions.setState({
      order,
      comments_data: [],
      pagination: null,
    });

    actions.loadData();
  },

  /**
   * 加载评论
   */
  loadData: () => (state, actions) => {
    // 从页面中加载评论数据
    const loadFromPage = () => {
      const comments = window.G_COMMENTS;

      if (comments) {
        actions.setState({
          comments_data: comments.data,
          pagination: comments.pagination,
          loading: false,
        });
        window.G_COMMENTS = null;
      }

      return comments;
    };

    // ajax 加载评论数据
    const loadFromServer = () => {
      actions.setState({ loading: true });

      getComments(state.commentable_type, state.commentable_id, state.order)
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then((response) => {
          actions.setState({
            comments_data: response.data,
            pagination: response.pagination,
          });
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  infiniteLoadInPage: () => (state, actions) => {
    actions.infiniteLoad({ isInDialog: false });
  },

  infiniteLoadInDialog: () => (state, actions) => {
    actions.infiniteLoad({ isInDialog: true });
  },

  /**
   * 下拉加载更多
   */
  infiniteLoad: ({ isInDialog }) => (state, actions) => {
    if (state.loading) {
      return;
    }

    const { pagination } = state;

    if (pagination.page >= pagination.pages) {
      return;
    }

    if (isInDialog) {
      if (
        $commentsWrapper[0].scrollHeight -
          $commentsWrapper[0].scrollTop -
          $commentsWrapper[0].clientHeight >
        100
      ) {
        return;
      }
    } else if (
      document.body.scrollHeight - window.pageYOffset - window.innerHeight >
      100
    ) {
      return;
    }

    actions.setState({ loading: true });

    getComments(
      state.commentable_type,
      state.commentable_id,
      state.order,
      pagination.page + 1,
    )
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then((response) => {
        actions.setState({
          comments_data: state.comments_data.concat(response.data),
          pagination: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 切换 comments_data 中的字段值
   * @param comment_id
   * @param fieldName 字段名
   * @param value 若为 undefined，则表示切换布尔值，否则设置为 value 值
   */
  toggleState: ({ comment_id, fieldName, value = undefined }) => (
    state,
    actions,
  ) => {
    const { comments_data } = state;
    const index = findIndex(comments_data, 'comment_id', comment_id);
    const comment = comments_data[index];

    comments_data[index][fieldName] = isUndefined(value)
      ? !comment[fieldName]
      : value;

    actions.setState({ comments_data });

    // 如果是打开回复列表，则开始加载回复列表
    if (
      fieldName === 'show_replies' &&
      comment[fieldName] &&
      !comment.replies_pagination
    ) {
      actions.loadReplies({ comment });
    }
  },

  /**
   * 加载回复列表
   */
  loadReplies: ({ comment }) => (state, actions) => {
    const isLoaded =
      !comment.replies_loading &&
      comment.replies_pagination &&
      comment.replies_pagination.page === comment.replies_pagination.pages;
    const isLoading = comment.replies_loading;

    if (isLoading || isLoaded) {
      return;
    }

    actions.toggleState({
      comment_id: comment.comment_id,
      fieldName: 'replies_loading',
      value: true,
    });

    getReplies({
      comment_id: comment.comment_id,
      page: comment.replies_pagination
        ? comment.replies_pagination.page + 1
        : 1,
      per_page: 10,
      order: 'create_time',
      include: ['user', 'voting'],
    })
      .finally(() => {
        actions.toggleState({
          comment_id: comment.comment_id,
          fieldName: 'replies_loading',
          value: false,
        });
      })
      .then((response) => {
        actions.toggleState({
          comment_id: comment.comment_id,
          fieldName: 'replies_data',
          value: comment.replies_data.concat(response.data),
        });
        actions.toggleState({
          comment_id: comment.comment_id,
          fieldName: 'replies_pagination',
          value: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 发表回复
   */
  submitReply: ({ target, comment }) => (state, actions) => {
    const $element = $(target).parent();
    const $button = $element.find('.submit');

    if ($button[0].disabled) {
      return;
    }

    const $textarea = $element.find('textarea');
    const value = $textarea.val();
    if (!value) {
      mdui.snackbar('请输入回复内容');
      return;
    }

    const params = {
      comment_id: comment.comment_id,
      content: value,
      include: ['user', 'voting'],
    };

    actions.toggleState({
      comment_id: comment.comment_id,
      fieldName: 'reply_submitting',
      value: true,
    });

    createReply(params)
      .finally(() => {
        actions.toggleState({
          comment_id: comment.comment_id,
          fieldName: 'reply_submitting',
          value: false,
        });
      })
      .then(() => {
        mdui.snackbar('回复发布成功');

        // 发表成功后，隐藏回复框，回复数量+1
        actions.toggleState({
          comment_id: comment.comment_id,
          fieldName: 'show_new_reply',
        });
        actions.toggleState({
          comment_id: comment.comment_id,
          fieldName: 'reply_count',
          value: comment.reply_count + 1,
        });
        // todo
      })
      .catch((response) => {
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          mdui.snackbar(response.errors.content);
          return;
        }

        apiCatch(response);
      });
  },
};

export default extend(as, commonActions, voteActions, userPopoverActions);
