import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import { update as updateAnswer } from 'mdclub-sdk-js/es/AnswerApi';
import commonActions from '~/utils/actionsAbstract';
import editorActions from '~/components/editor/actions';
import topicSelectorActions from '~/components/editor/components/topic-selector/actions';
import { loadStart, loadEnd } from '~/utils/loading';
import apiCatch from '~/utils/errorHandler';
import { emit } from '~/utils/pubsub';

const as = {
  /**
   * 提交保存
   */
  submit: () => (state, actions) => {
    loadStart();

    const $editor = $('.mc-answer-edit .mc-editor');
    const answer_id = parseInt($editor.data('primary_id'), 10);
    const content_rendered = $editor.data('editor-instance').getHTML();

    updateAnswer({
      answer_id,
      content_rendered,
      include: ['user', 'question'],
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
