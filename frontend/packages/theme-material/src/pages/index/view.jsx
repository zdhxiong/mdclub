import { h } from 'hyperapp';
import './index.less';

import Header from './components/header/view.jsx';
import Topics from './components/topics/view.jsx';
import Items from './components/items/view.jsx';

export default (state, actions) => ({ match }) => (
  <div
    oncreate={actions.onCreate}
    ondestroy={actions.onDestroy}
    key={match.url}
    id="page-index"
    class="mdui-container"
  >
    <Header title="推荐话题" url="/topics#recommended" />
    <Topics state={state} actions={actions} />

    <div class="items-wrapper">
      <Items
        title="最近更新提问"
        items={state.questions_recent_data}
        primaryKey="question_id"
        dataName="questions_recent_data"
        loading={state.questions_recent_loading}
        url="/questions"
        actions={actions}
      />
      <Items
        title="最近热门提问"
        items={state.questions_popular_data}
        primaryKey="question_id"
        dataName="questions_popular_data"
        loading={state.questions_popular_loading}
        url="/questions#popular"
        actions={actions}
      />
    </div>

    <div class="items-wrapper">
      <Items
        title="最新文章"
        items={state.articles_recent_data}
        primaryKey="article_id"
        dataName="articles_recent_data"
        loading={state.articles_recent_loading}
        url="/articles"
        actions={actions}
      />
      <Items
        title="最近热门文章"
        items={state.articles_popular_data}
        primaryKey="article_id"
        dataName="articles_popular_data"
        loading={state.articles_popular_loading}
        url="/articles#popular"
        actions={actions}
      />
    </div>
  </div>
);
