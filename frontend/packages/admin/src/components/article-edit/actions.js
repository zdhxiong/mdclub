import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import { update as updateArticle } from 'mdclub-sdk-js/es/ArticleApi';
import commonActions from '~/utils/actionsAbstract';
import editorActions from '~/components/editor/actions';
import topicSelectorActions from '~/components/editor/components/topic-selector/actions';
import { loadStart, loadEnd } from '~/utils/loading';
import { apiCatch } from '~/utils/errorHandlers';
import { emit } from '~/utils/pubsub';

const as = {
  /**
   * 提交保存
   */
  submit: () => (state, actions) => {
    loadStart();

    const $editor = $('.mc-article-edit .mc-editor');
    const article_id = parseInt($editor.data('primary_id'), 10);
    const title = $editor.find('.editor-title').val();
    const content_rendered = $editor.data('editor-instance').getHTML();

    updateArticle({
      article_id,
      title,
      topic_ids: state.editor_selected_topic_ids,
      content_rendered,
      include: ['user', 'topics'],
    })
      .finally(() => {
        loadEnd();
      })
      .then(({ data }) => {
        actions.editorClose();
        mdui.snackbar('修改成功');

        emit('datatable_update_row', data);
      })
      .catch(apiCatch);
  },
};

export default extend(as, commonActions, editorActions, topicSelectorActions);
