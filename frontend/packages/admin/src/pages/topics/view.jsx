import { h } from 'hyperapp';

import Datatable from '~/components/datatable/view.jsx';
import { emit } from '~/utils/pubsub';

export default (state, actions) => ({ match }) => (
  <div
    oncreate={actions.onCreate}
    ondestroy={actions.onDestroy}
    key={match.url}
    id="page-topics"
    class="mdui-container-fluid"
  >
    <Datatable loadData={actions.loadData} />

    <button
      class="mdui-fab mdui-fab-fixed mdui-color-theme"
      mdui-tooltip="{content: '添加话题'}"
      onclick={() => emit('topic_edit_open', null)}
    >
      <i class="mdui-icon material-icons">add</i>
    </button>
  </div>
);
