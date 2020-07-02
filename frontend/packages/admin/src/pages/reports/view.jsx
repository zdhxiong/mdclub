import { h } from 'hyperapp';

import Datatable from '~/components/datatable/view.jsx';

export default (state, actions) => ({ match }) => (
  <div
    oncreate={actions.onCreate}
    ondestroy={actions.onDestroy}
    key={match.url}
    id="page-reports"
    class="mdui-container-fluid"
  >
    <Datatable loadData={actions.loadData} />
  </div>
);
