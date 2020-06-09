import $ from 'mdui.jq';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import {
  get as getUser,
  deleteMyCover,
  deleteMyAvatar,
  updateMine,
  getArticles,
  getQuestions,
  getAnswers,
} from 'mdclub-sdk-js/es/UserApi';
import commonActions from '~/utils/actionsAbstract';
import userPopoverActions from '~/components/user-popover/actions';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import stateDefault, { tabs } from './stateDefault';

/**
 * 当前访问的用户ID
 */
let interviewee_id;

/**
 * 编辑个人资料的弹框实例
 */
let editInfoDialog;

const TABNAME_QUESTIONS = 'questions';
const TABNAME_ARTICLES = 'articles';
// const TABNAME_ANSWERS = 'answers';

let tab;
let tabIndex = tabs.indexOf(TABNAME_QUESTIONS);
let scroll_position;
const per_page = 20;

const as = {
  /**
   * 加载提问列表、回答列表或文字列表
   */
  getContexts: ({ tabName, page }) => (state) => {
    const params = {
      user_id: interviewee_id,
      page,
      per_page,
      include: ['user', 'topics', 'is_following'],
      order: state[`${tabName}_order`],
    };

    if (tabName === TABNAME_QUESTIONS) {
      return getQuestions(params);
    }

    if (tabName === TABNAME_ARTICLES) {
      return getArticles(params);
    }

    params.include = ['user', 'question', 'voting'];
    return getAnswers(params);
  },

  onCreate: (props) => (_, actions) => {
    emit('route_update');

    tab = new mdui.Tab('.mc-tab');
    tabIndex = tab.activeIndex;

    if (interviewee_id !== props.interviewee_id) {
      interviewee_id = props.interviewee_id;
      actions.setState(stateDefault);
      actions.loadInterviewee();
      actions.afterChangeTab();
    }

    tab.$element.on('change.mdui.tab', () => {
      tabIndex = tab.activeIndex;
      window.history.replaceState({}, '', `#${tabs[tabIndex]}`);
      actions.afterChangeTab();
    });

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }

    // 绑定加载更多
    $window.on('scroll', actions.infiniteLoad);
  },

  onDestroy: () => (_, actions) => {
    $window.off('scroll', actions.infiniteLoad);
  },

  /**
   * 加载当前被访问用户数据
   */
  loadInterviewee: () => (state, actions) => {
    // 访问自己的页面
    if (state.user && interviewee_id === state.user.user_id) {
      actions.setTitle(`${state.user.username}的个人主页`);
      actions.setState({ interviewee: state.user });
      window.G_INTERVIEWEE = null;
      return;
    }

    // 从页面中加载用户数据
    const interviewee = window.G_INTERVIEWEE;
    if (interviewee) {
      actions.setState({ interviewee });
      window.G_INTERVIEWEE = null;
      actions.setTitle(`${interviewee.username}的个人主页`);
      return;
    }

    // 从服务器加载用户数据
    actions.setState({
      loading: true,
      interviewee: null,
    });

    getUser({
      user_id: interviewee_id,
      include: ['is_following'],
    })
      .finally(() => {
        actions.setState({ loading: false });
      })
      .then(({ data }) => {
        actions.setState({ interviewee: data });
        actions.setTitle(`${data.username}的个人主页`);
      })
      .catch(apiCatch);
  },

  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[tabIndex];
    const TAB_NAME = tabName.toUpperCase();

    actions.setState({ current_tab: tabName });

    if (state[`${tabName}_pagination`]) {
      return;
    }

    // 从页面中加载初始数据
    if (window[`G_USER_${TAB_NAME}`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_USER_${TAB_NAME}`].data,
        [`${tabName}_pagination`]: window[`G_USER_${TAB_NAME}`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_USER_${TAB_NAME}`] = null;

      // 若第一页没有填满屏幕，则加载第二页
      setTimeout(() => {
        actions.infiniteLoad();
      });

      return;
    }

    // ajax 加载数据
    actions.setState({
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: null,
      [`${tabName}_loading`]: true,
    });

    actions
      .getContexts({ tabName, page: 1 })
      .finally(() => {
        actions.setState({ [`${tabName}_loading`]: false });
      })
      .then((response) => {
        actions.setState({
          [`${tabName}_data`]: response.data,
          [`${tabName}_pagination`]: response.pagination,
        });

        // 若第一页没有填满屏幕，则加载第二页
        setTimeout(() => {
          actions.infiniteLoad();
        });
      })
      .catch(apiCatch);
  },

  /**
   * 绑定下拉加载更多
   */
  infiniteLoad: () => (state, actions) => {
    const tabName = tabs[tabIndex];

    if (state[`${tabName}_loading`]) {
      return;
    }

    const pagination = state[`${tabName}_pagination`];

    if (pagination.page >= pagination.pages) {
      return;
    }

    if (
      document.body.scrollHeight - window.pageYOffset - window.innerHeight >
      100
    ) {
      return;
    }

    actions.setState({ [`${tabName}_loading`]: true });

    actions
      .getContexts({ tabName, page: pagination.page + 1 })
      .finally(() => {
        actions.setState({ [`${tabName}_loading`]: false });
      })
      .then((response) => {
        actions.setState({
          [`${tabName}_data`]: state[`${tabName}_data`].concat(response.data),
          [`${tabName}_pagination`]: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 点击链接后，保存最后访问的提问ID和提问详情
   * @param context 提问或文字信息
   */
  afterItemClick: (context) => (_, actions) => {
    const tabName = tabs[tabIndex];

    scroll_position = window.pageYOffset;

    if (tabName === TABNAME_QUESTIONS) {
      window.G_QUESTION = context;
      actions.setState({ last_visit_question_id: context.question_id });
    } else if (tabName === TABNAME_ARTICLES) {
      window.G_ARTICLE = context;
      actions.setState({ last_visit_article_id: context.article_id });
    } else {
      window.G_ANSWER = context;
      actions.setState({ last_visit_answer_id: context.answer_id });
    }
  },

  /**
   * 切换排序方式
   */
  changeOrder: (order) => (state, actions) => {
    const tabName = tabs[tabIndex];

    if (order === state[`${tabName}_order`]) {
      return;
    }

    actions.setState({
      [`${tabName}_order`]: order,
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: null,
    });

    actions.afterChangeTab();
  },

  /**
   * 删除头像
   */
  deleteAvatar: (e) => (state, actions) => {
    e.preventDefault();

    mdui.confirm(
      '系统将删除现有头像，并随机生成一个新头像。',
      '要删除现有头像吗？',
      () => {
        deleteMyAvatar()
          .then(({ data }) => {
            const { user } = state;
            user.avatar = data;
            actions.setState({ user });
          })
          .catch(apiCatch);
      },
      () => {},
      {
        confirmText: '删除',
        cancelText: '取消',
      },
    );
  },

  /**
   * 删除封面
   */
  deleteCover: (e) => (state, actions) => {
    e.preventDefault();

    mdui.confirm(
      '系统将删除现有封面，并重置为默认封面。',
      '要删除现有封面吗？',
      () => {
        deleteMyCover()
          .then(({ data }) => {
            const { user } = state;
            user.cover = data;
            actions.setState({ user });
          })
          .catch(apiCatch);
      },
      () => {},
      {
        confirmText: '删除',
        cancelText: '取消',
      },
    );
  },

  /**
   * 更新个人信息
   */
  updateUserInfo: () => (_, actions) => {
    if (editInfoDialog) {
      editInfoDialog.open();
      return;
    }

    const $dialog = $('#page-user .edit-info');

    editInfoDialog = new mdui.Dialog($dialog, {
      closeOnConfirm: false,
    });

    $dialog.on('open.mdui.dialog', () => {
      $dialog.find('.mdui-textfield-input')[0].focus();
    });

    $dialog.on('opened.mdui.dialog', () => {
      editInfoDialog.handleUpdate();
    });

    $dialog.on('confirm.mdui.dialog', () => {
      const formData = {
        include: ['is_following'],
      };

      $dialog
        .find('form')
        .serializeArray()
        .forEach((item) => {
          formData[item.name] = item.value;
        });

      actions.setState({ edit_info_submitting: true });

      updateMine(formData)
        .finally(() => {
          actions.setState({ edit_info_submitting: false });
        })
        .then(({ data }) => {
          const updateData = { user: data };

          if (data.user_id === interviewee_id) {
            updateData.interviewee = data;
          }

          actions.setState(updateData);
          editInfoDialog.close();
          mdui.snackbar('更新成功');
        })
        .catch(apiCatch);
    });

    editInfoDialog.open();
  },
};

export default extend(as, commonActions, userPopoverActions);
