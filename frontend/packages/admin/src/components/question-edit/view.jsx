import { h } from 'hyperapp';

import Editor from '~/components/editor/view.jsx';
import TopicSelector from '~/components/editor/components/topic-selector/view.jsx';

export default ({ state, actions }) => (
  <div class="mc-question-edit">
    <Editor
      id="page-questions-editor"
      title="编辑提问"
      withTitle={true}
      withTopics={true}
      onSubmit={actions.submit}
      publishing={state.publishing}
      state={state}
      actions={actions}
    />
    <TopicSelector state={state} actions={actions} />
  </div>
);
