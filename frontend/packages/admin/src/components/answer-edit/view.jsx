import { h } from 'hyperapp';

import Editor from '~/components/editor/view.jsx';

export default ({ state, actions }) => (
  <div class="mc-answer-edit">
    <Editor
      id="page-answer-editor"
      title="编辑回答"
      withTitle={false}
      withTopics={false}
      onSubmit={actions.submit}
      publishing={state.publishing}
      state={state}
      actions={actions}
    />
  </div>
);
