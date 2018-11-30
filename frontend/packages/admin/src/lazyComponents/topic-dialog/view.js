import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../components/loading';

export default () => (global_state, global_actions) => {
  const actions = global_actions.lazyComponents.topicDialog;
  const state = global_state.lazyComponents.topicDialog;

  return (
    <div class="mdui-dialog mc-topic-dialog">
      {!state.loading && state.topic ?
      <div
        class="header"
        oncreate={element => actions.headerInit(element)}
        style={{
          backgroundImage: `url(${state.topic.cover.m})`,
        }}
      >
        <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient"></div>
        <div class="name">{state.topic.name}</div>
      </div> : ''}
      {!state.loading && state.topic ?
      <div class="body">

      </div> : ''}
      {state.loading ? <Loading/> : ''}
    </div>
  );
};
