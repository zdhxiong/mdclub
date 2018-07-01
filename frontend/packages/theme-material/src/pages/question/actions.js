import mdui from 'mdui';
import $ from 'mdui.JQ';
import QuestionService from '../../service/Question';
import AnswerService from '../../service/Answer';
import QuestionFollowService from '../../service/QuestionFollow';

let global_actions;
let question_id;
let EditorDialog;
let EditorInstance;
let $editorDialog;

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
    question_id = props.question_id;

    // 加载初始数据
    actions.loadQuestion();
    actions.loadAnswer();

    // 初始化编辑器
    $editorDialog = $('#page-answer-editor');
    EditorInstance = $editorDialog.find('.mc-editor').data('mc-editor-instance');
    EditorDialog = new mdui.Dialog($editorDialog, {
      history: false,
    });

    // 打开编辑器后聚焦到输入区域
    $editorDialog.on('opened.mdui.dialog', () => {
      EditorInstance.initSelection();
    });

    $(window).on('scroll', actions.infiniteLoadAnswer);
  },

  /**
   * 加载问题数据
   */
  loadQuestion: () => (state, actions) => {
    // 从页面中加载问题数据
    const loadFromPage = () => {
      const question = window.G_QUESTION;

      if (question) {
        actions.setState({ question });
        window.G_QUESTION = null;
        actions.setTitle(question.title);
      }

      return question;
    };

    // 从问题列表中获取问题数据
    const loadFromQuestions = () => {
      const questions_state = global_actions.questions.getState();
      let questions = [];
      let question = null;

      questions_state.tabs.forEach((tab) => {
        questions = questions.concat(questions_state[`${tab}_data`]);
      });

      questions.forEach((_question) => {
        if (question_id === _question.question_id) {
          question = _question;
        }
      });

      if (question) {
        actions.setState({ question });
        actions.setTitle(question.title);
      }

      return question;
    };

    // ajax 加载问题数据
    const loadFromServer = () => {
      actions.setState({
        question_loading: true,
      });

      const loaded = (response) => {
        actions.setState({
          question_loading: false,
        });

        if (response.code) {
          mdui.snackbar(response.message);
          return;
        }

        actions.setState({
          question: response.data,
        });
        actions.setTitle(response.data.title);
      };

      QuestionService.getOne(question_id, loaded);
    };

    if (loadFromPage()) {
      return;
    }

    if (loadFromQuestions()) {
      return;
    }

    loadFromServer();
  },

  /**
   * 加载回答数据
   * @param cb AJAX加载完毕后的回调函数
   * @returns void
   */
  loadAnswer: (cb = () => {}) => (state, actions) => {
    // 从页面中加载回答数据
    const loadFromPage = () => {
      const answers = window.G_ANSWERS;

      if (answers) {
        actions.setState({
          answer_data: answers.data,
          answer_pagination: answers.pagination,
          answer_loading: false,
        });
        window.G_ANSWERS = null;
      }

      return answers;
    };

    // 从 ajax 获取回答数据
    const loadFromServer = () => {
      actions.setState({
        answer_loading: true,
      });

      const loaded = (response) => {
        actions.setState({
          answer_loading: false,
        });

        if (response.code) {
          mdui.snackbar(response.message);
          return;
        }

        actions.setState({
          answer_data: response.data,
          answer_pagination: response.pagination,
        });

        cb();
      };

      AnswerService.getListByQuestionId(question_id, {}, loaded);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  /**
   * 绑定下拉加载更多回答
   */
  infiniteLoadAnswer: () => (_, actions) => {
    const state = actions.getState();
    const pagination = state.answer_pagination;

    if (state.answer_loading || !pagination) {
      return;
    }

    if (pagination.page >= pagination.total_page) {
      return;
    }

    if (document.body.scrollHeight - window.pageYOffset - window.innerHeight > 100) {
      return;
    }

    actions.setState({
      answer_loading: true,
    });

    const loaded = (response) => {
      actions.setState({
        answer_loading: false,
      });

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        answer_data: state.answer_data.concat(response.data),
        answer_pagination: response.pagination,
      });
    };

    const data = {
      page: pagination.page + 1,
    };

    AnswerService.getListByQuestionId(question_id, data, loaded);
  },

  /**
   * 发布回答
   */
  publishAnswer: Editor => (_, actions) => {
    const content_text = Editor.getText();
    const content_rendered = Editor.getHTML();

    if (!content_text) {
      EditorInstance.initSelection();
      return;
    }

    actions.setState({
      answer_publishing: true,
    });

    AnswerService.create(question_id, { content_rendered }, (response) => {
      actions.setState({
        answer_publishing: false,
      });

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      // 关闭编辑器
      EditorDialog.close();

      // 清空草稿
      localStorage.removeItem(`answer-content-${question_id}`);
      EditorInstance.clear();

      // 重新加载回答列表
      actions.loadAnswer(() => {
        // 更新问题列表信息
        const state = actions.getState();
        const question = state.question;
        question.answer_time = response.data.create_time;
        question.answer_count = state.answer_pagination.total;

        actions.setState({ question });
        global_actions.questions.questionUpdate(question);
      });

      // 滚动条滚动到回答的位置
      window.scrollTo(0, $('.answers-count').offset().top - $('.mdui-toolbar').height());
    });
  },

  /**
   * 舍弃回答草稿
   */
  clearDrafts: () => {
    EditorDialog.close();
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
   * 切换关注状态
   */
  toggleFollow: () => (state, actions) => {
    if (!global_actions.getState().user.user.user_id) {
      global_actions.components.login.open();

      return;
    }

    const question = state.question;
    question.relationship.is_following = !question.relationship.is_following;
    actions.setState({ question });

    const done = (response) => {
      if (!response.code) {
        global_actions.questions.followUpdate();
        global_actions.questions.questionUpdate(question);
        return;
      }

      mdui.snackbar(response.message);

      question.relationship.is_following = !question.relationship.is_following;
      actions.setState({ question });
    };

    if (question.relationship.is_following) {
      QuestionFollowService.addFollow(question_id, done);
    } else {
      QuestionFollowService.deleteFollow(question_id, done);
    }
  },
};
