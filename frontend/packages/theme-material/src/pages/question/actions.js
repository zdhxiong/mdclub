import { location } from 'hyperapp-router';
import mdui from 'mdui';
import extend from 'mdui.jq/es/functions/extend';
import { $window } from 'mdui/es/utils/dom';
import { unescape } from 'html-escaper';
import {
  get as getQuestion,
  createAnswer,
  getAnswers,
} from 'mdclub-sdk-js/es/QuestionApi';
import {
  getList as getAnswerList,
  get as getAnswer,
} from 'mdclub-sdk-js/es/AnswerApi';
import { COMMON_FIELD_VERIFY_FAILED } from 'mdclub-sdk-js/es/errors';
import { emit } from '~/utils/pubsub';
import { fullPath } from '~/utils/path';
import apiCatch from '~/utils/errorHandler';
import commonActions from '~/utils/actionsAbstract';
import editorActions from '~/components/editor/actions';
import voteActions from '~/components/vote/actions';
import userPopoverActions from '~/components/user-popover/actions';
import stateDefault from './stateDefault';

const per_page = 20;

const as = {
  /**
   * @param question_id
   * @param answer_id 提问详情页该参数值为 0
   * @param route question, answer
   */
  onCreate: ({ question_id, answer_id, route }) => (state, actions) => {
    emit('route_update');

    actions.setState({ route });

    if (answer_id) {
      // 回答详情页
      if (state.answer_id !== answer_id) {
        actions.setState(stateDefault);
        actions.setState({ question_id, answer_id });
        actions.loadQuestion();
        actions.loadAnswer();
      }
    } else {
      // 提问详情页
      if (state.question_id !== question_id || state.answer_id) {
        actions.setState(stateDefault);
        actions.setState({ question_id });
        actions.loadQuestion();
        actions.loadAnswers();
      }

      // 发表回答后，到了回答页面，然后返回到提问页面，需要重新加载回答列表
      if (!state.answer_pagination) {
        actions.loadAnswers();
      }

      $window.on('scroll', actions.infiniteLoadAnswers);
    }
  },

  onDestroy: () => (state, actions) => {
    if (state.route === 'question') {
      $window.off('scroll', actions.infiniteLoadAnswers);
    }
  },

  /**
   * 加载问题数据
   */
  loadQuestion: () => (state, actions) => {
    const setTitle = (title) => {
      if (state.route === 'question') {
        actions.setTitle(title);
      } else {
        actions.setTitle(`${title} 的回答`);
      }
    };

    // 从页面中加载问题数据
    const loadFromPage = () => {
      const question = window.G_QUESTION;

      if (question) {
        actions.setState({ question });
        window.G_QUESTION = null;
        setTitle(unescape(question.title));
      }

      return question;
    };

    // ajax 加载问题数据
    const loadFromServer = () => {
      actions.setState({ loading: true });

      getQuestion({
        question_id: state.question_id,
        include: ['user', 'topics', 'is_following', 'voting'],
      })
        .finally(() => {
          actions.setState({ loading: false });
        })
        .then(({ data }) => {
          actions.setState({ question: data });
          setTitle(unescape(data.title));
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  /**
   * 加载回答数据
   */
  loadAnswer: () => (state, actions) => {
    // 从页面中加载回答数据
    const loadFromPage = () => {
      const answer = window.G_ANSWER;

      if (answer) {
        actions.setState({ answer_data: [answer] });
        window.G_ANSWER = null;
      }

      return answer;
    };

    // ajax 加载回答数据
    const loadFromServer = () => {
      actions.setState({ answer_loading: true });

      getAnswer({
        answer_id: state.answer_id,
        include: ['user', 'voting'],
      })
        .finally(() => {
          actions.setState({ answer_loading: false });
        })
        .then(({ data }) => {
          actions.setState({ answer_data: [data] });
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  /**
   * 切换回答排序方式
   */
  changeOrder: (answer_order) => (state, actions) => {
    if (answer_order === state.answer_order) {
      return;
    }

    actions.setState({
      answer_order,
      answer_data: [],
      answer_pagination: null,
    });

    actions.loadAnswers();
  },

  /**
   * 加载回答数据
   * @returns void
   */
  loadAnswers: () => (state, actions) => {
    // 从页面中加载回答数据
    const loadFromPage = () => {
      const answers = window.G_QUESTION_ANSWERS;

      if (answers) {
        actions.setState({
          answer_data: answers.data,
          answer_pagination: answers.pagination,
          answer_loading: false,
        });
        window.G_QUESTION_ANSWERS = null;
      }

      return answers;
    };

    // 从 ajax 获取回答数据
    const loadFromServer = () => {
      actions.setState({ answer_loading: true });

      getAnswers({
        question_id: state.question_id,
        per_page,
        order: state.answer_order,
        include: ['user', 'voting'],
      })
        .finally(() => {
          actions.setState({ answer_loading: false });
        })
        .then((response) => {
          actions.setState({
            answer_data: response.data,
            answer_pagination: response.pagination,
          });
        })
        .catch(apiCatch);
    };

    if (loadFromPage()) {
      return;
    }

    loadFromServer();
  },

  /**
   * 绑定下拉加载更多回答
   */
  infiniteLoadAnswers: () => (state, actions) => {
    if (state.answer_loading) {
      return;
    }

    const pagination = state.answer_pagination;

    if (pagination.page >= pagination.pages) {
      return;
    }

    if (
      document.body.scrollHeight - window.pageYOffset - window.innerHeight >
      100
    ) {
      return;
    }

    actions.setState({ answer_loading: true });

    getAnswerList({
      page: pagination.page + 1,
      per_page,
      order: state.answer_order,
      include: ['user', 'voting'],
      question_id: state.question_id,
    })
      .finally(() => {
        actions.setState({ answer_loading: false });
      })
      .then((response) => {
        actions.setState({
          answer_data: state.answer_data.concat(response.data),
          answer_pagination: response.pagination,
        });
      })
      .catch(apiCatch);
  },

  /**
   * 发布回答
   */
  publishAnswer: ({ content }) => (state, actions) => {
    const { auto_save_key } = state;

    if (!content || content === '<p><br></p>') {
      mdui.snackbar('请输入正文');
      return;
    }

    actions.setState({ answer_publishing: true });

    createAnswer({
      question_id: state.question_id,
      content_rendered: content,
      include: ['user', 'voting'],
    })
      .finally(() => {
        actions.setState({ answer_publishing: false });
      })
      .then((response) => {
        window.localStorage.removeItem(`${auto_save_key}-content`);
        actions.editorClose();

        // 发表回答后，回答数量 + 1
        const { question } = state;
        question.answer_count += 1;

        // 下次进入重新加载回答
        actions.setState({
          question,
          answer_data: [],
          answer_pagination: null,
        });

        // 到回答详情页
        window.G_QUESTION = state.question;
        window.G_ANSWER = response.data;
        location.actions.go(
          fullPath(
            `/questions/${state.question_id}/answers/${response.data.answer_id}`,
          ),
        );
      })
      .catch((response) => {
        if (response.code === COMMON_FIELD_VERIFY_FAILED) {
          mdui.snackbar(Object.values(response.errors)[0]);
          return;
        }

        apiCatch(response);
      });
  },
};

export default extend(
  as,
  commonActions,
  editorActions,
  voteActions,
  userPopoverActions,
);
