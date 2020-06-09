import { h } from 'hyperapp';
import './index.less';

import Nav from '~/components/nav/view.jsx';
import Topic from './components/topic/view.jsx';
import Contexts from './components/contexts/view.jsx';

export default (state, actions) => ({ match }) => {
  const topic_id = parseInt(match.params.topic_id, 10);
  const { topic, loading } = state;

  return (
    <div
      oncreate={() => actions.onCreate({ topic_id })}
      ondestroy={actions.onDestroy}
      key={match.url}
      id="page-topic"
      class="mdui-container"
    >
      <Nav path="/topics" />
      <Topic topic={topic} loading={loading} actions={actions} />
      <Contexts state={state} actions={actions} />
    </div>
  );
};
