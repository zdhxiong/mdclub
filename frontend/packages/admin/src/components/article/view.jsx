import { h } from 'hyperapp';
import cc from 'classcat';
import { richText, summaryText } from '~/utils/html';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import TopicsBar from '~/components/topics-bar/view.jsx';

export default ({ state, actions }) => {
  const { loading, article } = state;

  return (
    <div
      class="mc-article mdui-dialog"
      oncreate={(element) => actions.onCreate({ element })}
    >
      <Loading show={loading} />

      <If condition={article}>
        <div class="mdui-dialog-title">
          <span
            oncreate={summaryText(article.title)}
            onupdate={summaryText(article.title)}
          />
          <div class="actions">
            <button
              class="mdui-fab mdui-fab-mini mdui-color-theme"
              mdui-tooltip="{content: '编辑', delay: 300}"
              onclick={actions.toEdit}
            >
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button
              class="delete mdui-fab mdui-fab-mini"
              mdui-tooltip="{content: '删除', delay: 300}"
              onclick={actions.delete}
            >
              <i class="mdui-icon material-icons">delete</i>
            </button>
          </div>
        </div>
        <div class="mdui-dialog-content mdui-text-color-theme-text">
          <div
            class="content mdui-typo"
            oncreate={richText(article.content_rendered)}
            onupdate={richText(article.content_rendered)}
          />
          <If condition={article.relationships.topics.length}>
            <TopicsBar topics={article.relationships.topics} />
          </If>
        </div>
      </If>
      <div class={cc(['mdui-dialog-actions', { 'mdui-hidden': !article }])}>
        <button class="mdui-btn mdui-ripple" mdui-dialog-close>
          关闭
        </button>
      </div>
    </div>
  );
};
