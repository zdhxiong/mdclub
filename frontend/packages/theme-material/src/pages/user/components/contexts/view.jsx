import { h } from 'hyperapp';
import './index.less';

import ListHeader from '~/components/list-header/view.jsx';
import Empty from '~/components/empty/view.jsx';
import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Tab from '~/components/tab/view.jsx';
import {
  QuestionItem,
  ArticleItem,
  AnswerItem,
} from '~/components/list-item/view.jsx';

export default ({ state, actions }) => (
  <div class="contexts mdui-card">
    <Tab
      items={[
        {
          name: '提问',
          hash: 'questions',
          count: state.interviewee ? state.interviewee.question_count : 0,
        },
        {
          name: '回答',
          hash: 'answers',
          count: state.interviewee ? state.interviewee.answer_count : 0,
        },
        {
          name: '文章',
          hash: 'articles',
          count: state.interviewee ? state.interviewee.article_count : 0,
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
                  order: '-create_time',
                  name: '发布时间（从晚到早）',
                },
                {
                  order: 'create_time',
                  name: '发布时间（从早到晚）',
                },
                {
                  order: '-vote_count',
                  name: '最热门',
                },
              ]}
              onChangeOrder={actions.changeOrder}
            />
          </If>
          <If condition={tabName === 'answers'}>
            <ListHeader
              show={!isEmpty}
              title={`共 ${pagination ? pagination.total : 0} 个回答`}
              disabled={isLoading}
              currentOrder={order}
              key={tabName}
              orders={[
                {
                  order: '-create_time',
                  name: '发布时间（从晚到早）',
                },
                {
                  order: 'create_time',
                  name: '发布时间（从早到晚）',
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
              <If condition={tabName === 'answers'}>
                {data.map((context) => (
                  <AnswerItem
                    answer={context}
                    last_visit_id={state.last_visit_question_id}
                    tabName={tabName}
                    actions={actions}
                  />
                ))}
              </If>
            </div>
          </If>

          <Empty
            show={isEmpty}
            title={`该用户没有发表${
              // eslint-disable-next-line no-nested-ternary
              tabName === 'questions'
                ? '提问'
                : tabName === 'articles'
                ? '文章'
                : '回答'
            }`}
          />
          <Loading show={isLoading} />
          <Loaded show={isLoaded} />
        </div>
      );
    })}
  </div>
);
