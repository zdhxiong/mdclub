import mdui from 'mdui';
import { getList } from 'mdclub-sdk-js/es/TopicApi';
import each from 'mdui.jq/es/functions/each';
import apiCatch from '~/utils/errorHandler';

/**
 * 在含编辑器，且编辑器中含话题选择器的页面中，引入该 actions
 */
export default {
  /**
   * 打开对话框
   */
  topicSelectorOpen: () => (state, actions) => {
    const dialog = new mdui.Dialog('.mc-topic-selector', {
      history: false,
    });

    const $content = dialog.$element.find('.mdui-dialog-content');

    actions.setState({
      topics_data: [],
      topics_pagination: null,
      topics_loading: true,
    });

    dialog.open();

    const loaded = (promise) => {
      promise
        .finally(() => {
          actions.setState({ topics_loading: false });
        })
        .then(({ data, pagination }) => {
          actions.setState({
            topics_data: actions.getState().topics_data.concat(data),
            topics_pagination: pagination,
          });
        })
        .catch(apiCatch);
    };

    const infiniteLoad = () => {
      if (actions.getState().topics_loading) {
        return;
      }

      const { topics_pagination } = actions.getState();

      if (!topics_pagination) {
        return;
      }

      if (topics_pagination.page >= topics_pagination.pages) {
        return;
      }

      if (
        $content[0].scrollHeight -
          $content[0].scrollTop -
          $content[0].offsetHeight >
        100
      ) {
        return;
      }

      actions.setState({ topics_loading: true });

      loaded(
        getList({
          page: topics_pagination.page + 1,
          order: '-follower_count',
        }),
      );
    };

    loaded(getList({ order: '-follower_count' }));

    $content.on('scroll', infiniteLoad);

    dialog.$element.on('close.mdui.dialog', () => {
      $content.off('scroll', infiniteLoad);
    });
  },

  /**
   * 点击话题
   */
  topicSelectorChange: ({ event, dataIndex }) => (state, actions) => {
    const isChecked = event.target.checked;
    const {
      topics_data,
      editor_selected_topics,
      topics_max_selectable,
    } = state;

    if (isChecked && editor_selected_topics.length >= topics_max_selectable) {
      mdui.snackbar(`最多只能选择 ${topics_max_selectable} 个话题`);
      return;
    }

    if (isChecked) {
      const selectedTopicsResult = editor_selected_topics.concat([
        topics_data[dataIndex],
      ]);

      actions.setState({
        editor_selected_topics: selectedTopicsResult,
        editor_selected_topic_ids: selectedTopicsResult.map(
          (topic) => topic.topic_id,
        ),
      });
    } else {
      const dataIndexToSelectedIndex = (index, data, selected) => {
        const { topic_id } = data[index];
        let result;

        each(selected, (resultIndex, topic) => {
          if (topic.topic_id === topic_id) {
            result = resultIndex;
            return false;
          }
          return true;
        });

        return result;
      };

      const selectedIndex = dataIndexToSelectedIndex(
        dataIndex,
        topics_data,
        editor_selected_topics,
      );
      editor_selected_topics.splice(selectedIndex, 1);
      actions.setState({
        editor_selected_topics,
        editor_selected_topic_ids: editor_selected_topics.map(
          (topic) => topic.topic_id,
        ),
      });
    }

    actions.saveToLocalStorage();
  },

  /**
   * 移除选中的话题
   */
  topicSelectorRemoveOne: (selectedIndex) => (state, actions) => {
    const { editor_selected_topics: selected } = state;

    selected.splice(selectedIndex, 1);
    actions.setState({
      editor_selected_topics: selected,
      editor_selected_topic_ids: selected.map((topic) => topic.topic_id),
    });
    actions.saveToLocalStorage();
  },

  /**
   * 话题有变动后，保存到 localStorage
   */
  saveToLocalStorage: () => (state) => {
    window.localStorage.setItem(
      `${state.auto_save_key}-topics`,
      JSON.stringify(state.editor_selected_topics),
    );
  },
};
