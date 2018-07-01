import mdui from 'mdui';
import $ from 'mdui.JQ';
import { location } from '@hyperapp/router';
import QuestionService from '../../service/Question';
import QuestionFollowService from '../../service/QuestionFollow';

const TAB_INDEX = {
  RECENT: 0,
  POPULAR: 1,
  FOLLOWING: 2,
};
const tabs = ['recent', 'popular', 'following'];
let tabIndex;
let Tab;
let global_actions;
let scroll_position;
const is_updated = {
  [TAB_INDEX.RECENT]: false, // 最近更新列表是否有更新
  [TAB_INDEX.POPULAR]: false, // 最近热门列表是否有更新
  [TAB_INDEX.FOLLOWING]: false, // 我关注的列表是否有更新
};
let EditorDialog;
let $editorDialog;
let $editorTitle;

export default {
  setState: value => (value),
  getState: () => state => state,
  setTitle: (title) => {
    $('title').text(title);
  },

  /**
   * 初始化
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.theme.setPrimary('indigo');
    global_actions.routeChange(window.location.pathname);

    // 实例化 Tab
    Tab = new mdui.Tab('#tab-questions');
    tabIndex = Tab.activeIndex;

    // 绑定 Tab 切换
    Tab.$tab.on('change.mdui.tab', (event) => {
      tabIndex = event._detail.index;
      window.scrollTo(0, 0);
      history.replaceState({}, '', `#${tabs[tabIndex]}`);
      actions.afterChangeTab();
    });

    actions.afterChangeTab();

    // 恢复滚动条位置
    if (scroll_position) {
      window.scrollTo(0, scroll_position);
      scroll_position = 0;
    }

    // 绑定加载更多
    $(window).on('scroll', actions.infiniteLoad);

    $editorDialog = $('#page-questions-editor');
    $editorTitle = $editorDialog.find('.title');

    // 实时保存标题
    $editorTitle
      .val(localStorage.getItem('question-title'))
      .on('input', () => {
        localStorage.setItem('question-title', $editorTitle.val());
      });

    // 初始化编辑器
    EditorDialog = new mdui.Dialog($editorDialog, {
      history: false,
    });

    // 打开编辑器后聚焦到标题
    $editorDialog.on('opened.mdui.dialog', () => {
      $editorTitle[0].focus();
    });
  },

  /**
   * 切换 Tab 之后
   */
  afterChangeTab: () => (state, actions) => {
    const tabName = tabs[tabIndex];

    switch (tabName) {
      case 'recent':
        actions.setTitle('最新问题');
        break;
      case 'popular':
        actions.setTitle('近期热门问题');
        break;
      case 'following':
        actions.setTitle('我关注的问题');
        break;
      default:
        actions.setTitle('最新问题');
        break;
    }

    actions.setState({
      current_tab: tabName,
    });

    if (!is_updated[tabIndex] && state[`${tabName}_pagination`]) {
      return;
    }

    is_updated[tabIndex] = false;

    // 从页面中加载初始数据
    if (window[`G_${tabName.toUpperCase()}_QUESTIONS`]) {
      actions.setState({
        [`${tabName}_data`]: window[`G_${tabName.toUpperCase()}_QUESTIONS`].data,
        [`${tabName}_pagination`]: window[`G_${tabName.toUpperCase()}_QUESTIONS`].pagination,
      });

      // 清空页面中的数据，下次需要从 ajax 加载
      window[`G_${tabName.toUpperCase()}_QUESTIONS`] = false;

      return;
    }

    // ajax 加载数据
    actions.setState({
      [`${tabName}_data`]: [],
      [`${tabName}_pagination`]: false,
    });

    actions.loadStart(tabName);

    const loaded = (response) => {
      actions.loadEnd(tabName);

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        [`${tabName}_data`]: response.data,
        [`${tabName}_pagination`]: response.pagination,
      });
    };

    switch (tabIndex) {
      case TAB_INDEX.RECENT:
        QuestionService.getRecentList({}, loaded);
        break;

      case TAB_INDEX.POPULAR:
        QuestionService.getPopularList({}, loaded);
        break;

      case TAB_INDEX.FOLLOWING:
        QuestionFollowService.getMyFollowingQuestions({}, loaded);
        break;

      default:
        break;
    }
  },

  /**
   * 绑定下拉加载更多
   */
  infiniteLoad: () => (_, actions) => {
    const tabName = tabs[tabIndex];

    if (actions.getState()[`${tabName}_loading`]) {
      return;
    }

    const pagination = actions.getState()[`${tabName}_pagination`];

    if (!pagination) {
      return;
    }

    if (pagination.page >= pagination.total_page) {
      return;
    }

    if (document.body.scrollHeight - window.pageYOffset - window.innerHeight > 100) {
      return;
    }

    actions.loadStart(tabName);

    const loaded = (response) => {
      actions.loadEnd(tabName);

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        [`${tabName}_data`]: actions.getState()[`${tabName}_data`].concat(response.data),
        [`${tabName}_pagination`]: response.pagination,
      });
    };

    const data = {
      page: pagination.page + 1,
    };

    switch (tabIndex) {
      case TAB_INDEX.RECENT:
        QuestionService.getRecentList(data, loaded);
        break;

      case TAB_INDEX.POPULAR:
        QuestionService.getPopularList(data, loaded);
        break;

      case TAB_INDEX.FOLLOWING:
        QuestionFollowService.getMyFollowingQuestions(data, loaded);
        break;

      default:
        break;
    }
  },

  /**
   * 切换到最近更新
   */
  toRecent: () => {
    Tab.show(TAB_INDEX.RECENT);
  },

  /**
   * 切换到近期热门
   */
  toPopular: () => {
    Tab.show(TAB_INDEX.POPULAR);
  },

  /**
   * 切换到我关注的
   */
  toFolliwing: () => {
    Tab.show(TAB_INDEX.FOLLOWING);
  },

  /**
   * 在其他页面关注了问题后，需要调用该方法，以使下次切换到该页面时重新加载关注列表
   */
  followUpdate: () => {
    is_updated[TAB_INDEX.FOLLOWING] = true;
  },

  /**
   * 问题信息更新后（发布回答，关注），需要调用该方法
   * @param question
   */
  questionUpdate: question => (state, actions) => {
    let questions = [];

    state.tabs.forEach((tab) => {
      questions = state[`${tab}_data`];

      questions.forEach((_question, i) => {
        if (question.question_id === _question.question_id) {
          questions[i] = question;
        }
      });

      actions.setState({
        [`${tab}_data`]: questions,
      });
    });
  },

  /**
   * 打开编辑器
   */
  openEditor: () => {
    if (!global_actions.getState().user.user.user_id) {
      global_actions.components.login.open();

      return;
    }

    EditorDialog.open();
  },

  /**
   * 发布问题
   */
  publish: Editor => (state, actions) => {
    const title = $editorTitle.val();
    const content_rendered = Editor.getHTML();

    if (!title) {
      mdui.snackbar('请填写标题');
      $editorTitle[0].focus();
      return;
    }

    actions.setState({
      publishing: true,
    });

    QuestionService.create({
      title,
      content_rendered,
      topic_id: 0,
    }, (response) => {
      actions.setState({
        publishing: false,
      });

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      // 关闭编辑器
      EditorDialog.close();

      // 清空草稿和编辑器内容
      localStorage.removeItem('question-title');
      localStorage.removeItem('question-content');
      Editor.clear();
      $editorTitle.val('');

      // 标记列表需要更新
      is_updated[TAB_INDEX.RECENT] = true;
      is_updated[TAB_INDEX.FOLLOWING] = true;

      EditorDialog.$dialog.on('closed.mdui.dialog', () => {
        // 供问题详情页使用，减少 ajax 请求
        window.G_QUESTION = response.data;

        // 进入问题详情页
        location.actions.go($.path(`/questions/${response.data.question_id}`));
      });
    });
  },

  /**
   * 舍弃问题草稿
   */
  clearDrafts: () => {
    localStorage.removeItem('question-title');
    $editorTitle.val('');
    EditorDialog.close();
  },

  /**
   * 保存滚动条位置
   */
  saveScrollPosition: () => {
    scroll_position = window.pageYOffset;
  },

  /**
   * 开始加载
   */
  loadStart: tabName => ({
    [`${tabName}_loading`]: true,
  }),

  /**
   * 结束加载
   */
  loadEnd: tabName => ({
    [`${tabName}_loading`]: false,
  }),
};
