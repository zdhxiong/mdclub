import { h } from 'hyperapp';
import './index.less';

import ListHeader from '~/components/list-header/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Tab from '~/components/tab/view.jsx';
import { QuestionItem, ArticleItem } from '~/components/list-item/view.jsx';

export default ({ state, actions }) => (
  <div class="contexts mdui-card">
    <Tab
      items={[
        {
          name: '提问',
          hash: 'questions',
          count: state.topic ? state.topic.question_count : 0,
        },
        {
          name: '文章',
          hash: 'articles',
          count: state.topic ? state.topic.article_count : 0,
        },
      ]}
    />
    {state.tabs.map((tabName) => {
      const pagination = state[`${tabName}_pagination`];
      const data = state[`${tabName}_data`];
      const order = state[`${tabName}_order`];
      const isLoading = state[`${tabName}_loading`];
      const isEmpty = !isLoading && !data.length && pagination;
      const isLoaded =
        !isLoading && pagination && pagination.page === pagination.pages;

      return () => (
        <div id={tabName}>
          <If condition={tabName === 'questions'}>
            <ListHeader
              show={!isEmpty}
              title={`共 ${pagination ? pagination.total : 0} 个提问`}
              disabled={isLoading}
              currentOrder={order}
              key={tabName}
              orders={[
                {
                  order: '-update_time',
                  name: '更新时间（从晚到早）',
                },
                {
                  order: 'update_time',
                  name: '更新时间（从早到晚）',
                },
              ]}
              onChangeOrder={actions.changeOrder}
            />
          </If>
          <If condition={tabName === 'articles'}>
            <ListHeader
              show={!isEmpty}
              title={`共 ${pagination ? pagination.total : 0} 篇文章`}
              disabled={isLoading}
              currentOrder={order}
              key={tabName}
              orders={[
                {
                  order: '-update_time',
                  name: '更新时间（从晚到早）',
                },
                {
                  order: 'update_time',
                  name: '更新时间（从早到晚）',
                },
                {
                  order: '-vote_count',
                  name: '最热门',
                },
              ]}
              onChangeOrder={actions.changeOrder}
            />
          </If>

          <If condition={data.length}>
            <div class="item-list">
              <If condition={tabName === 'questions'}>
                {data.map((context) => (
                  <QuestionItem
                    question={context}
                    last_visit_id={state.last_visit_question_id}
                    tabName={tabName}
                    actions={actions}
                  />
                ))}
              </If>
              <If condition={tabName === 'articles'}>
                {data.map((context) => (
                  <ArticleItem
                    article={context}
                    last_visit_id={state.last_visit_article_id}
                    tabName={tabName}
                    actions={actions}
                  />
                ))}
              </If>
            </div>
          </If>

          <Empty
            show={isEmpty}
            title={`该话题下没有${tabName === 'questions' ? '提问' : '文章'}`}
          />
          <Loading show={isLoading} />
          <Loaded show={isLoaded} />
        </div>
      );
    })}
  </div>
);
