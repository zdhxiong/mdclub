import { h } from 'hyperapp';
import { richText, summaryText } from '~/utils/html';
import { emit } from '~/utils/pubsub';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import UserLine from '~/components/user-line/view.jsx';
import Follow from '~/components/follow/view.jsx';
import Vote from '~/components/vote/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';
import TopicsBar from '~/components/topics-bar/view.jsx';
import Comments from '~/components/comments/page.jsx';
import Nav from '~/components/nav/view.jsx';

export default (state, actions) => ({ match }) => {
  const article_id = parseInt(match.params.article_id, 10);
  const { article, loading } = state;

  return (
    <div
      oncreate={() => actions.onCreate({ article_id })}
      key={match.url}
      id="page-article"
      class="mdui-container"
    >
      <Nav path="/articles" />
      <div class="mdui-card mdui-card-shadow article">
        <If condition={article}>
          <h1
            class="title"
            oncreate={summaryText(article.title)}
            onupdate={summaryText(article.title)}
          />
          <UserLine
            actions={actions}
            user={article.relationships.user}
            time={article.create_time}
            dataName="article"
          />
          <div
            class="mdui-typo content"
            oncreate={richText(article.content_rendered)}
            onupdate={richText(article.content_rendered)}
          />
          <If condition={article.relationships.topics.length}>
            <TopicsBar topics={article.relationships.topics} />
          </If>
          <div class="actions">
            <Vote actions={actions} item={article} type="article" />
            <Follow item={article} type="article" actions={actions} />
            <div class="flex-grow" />
            <OptionsButton
              type="article"
              item={article}
              extraOptions={[
                {
                  name: `查看 ${article.follower_count} 位关注者`,
                  onClick: () => {
                    emit('users_dialog_open', {
                      type: 'article_followers',
                      id: article_id,
                    });
                  },
                },
              ]}
            />
          </div>
        </If>
        <Loading show={loading} />
      </div>

      <Comments commentable_type="article" commentable_id={article_id} />
    </div>
  );
};
