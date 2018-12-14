import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../components/loading';

const TextItem = ({ subheader, content, title }) => (
  <div class="text-item">
    <div class="text-subheader mdui-text-color-theme-secondary">{subheader}</div>
    <div class="text-content" title={title}>{content}</div>
  </div>
);

const CountItem = ({ label, count }) => (
  <div class="count-item">
    <div class="count-label mdui-text-color-theme-secondary">{label}</div>
    <div class="count-number">{count}</div>
  </div>
);

export default () => (global_state, global_actions) => {
  const actions = global_actions.lazyComponents.dialogTopic;
  const state = global_state.lazyComponents.dialogTopic;

  return (
    <div class="mdui-dialog mc-dialog-topic">
      {!state.loading && state.topic ?
      <div
        class="header"
        oncreate={element => actions.headerInit(element)}
        style={{
          backgroundImage: `url(${state.topic.cover.m})`,
        }}
      >
      </div> : ''}
      {!state.loading && state.topic ?
      <div class="body">
        <div class="mdui-row">
          <div class="label mdui-text-color-theme-text">话题信息</div>
          <div class="card mdui-card">
            <TextItem subheader="话题ID" content={state.topic.topic_id}/>
            <TextItem subheader="话题名称" content={state.topic.name}/>
            <TextItem subheader="描述" content={state.topic.description}/>
          </div>
        </div>
        <div class="mdui-row">
          <div class="label mdui-text-color-theme-text">统计</div>
          <div class="card mdui-card card-with-count">
            <div class="text-item">
              <div class="text-content mdui-clearfix">
                <CountItem label="文章" count={state.topic.article_count}/>
                <CountItem label="提问" count={state.topic.question_count}/>
                <CountItem label="关注者" count={state.topic.follower_count}/>
              </div>
            </div>
          </div>
        </div>
      </div> : ''}
      {state.loading ? <Loading/> : ''}
    </div>
  );
};
