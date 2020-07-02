import { h } from 'hyperapp';
import './index.less';

import Nav from '~/components/nav/view.jsx';
import Loading from '~/components/loading/view.jsx';
import Info from './components-page/info/view.jsx';
import Theme from './components-page/theme/view.jsx';
import Mail from './components-page/mail/view.jsx';
import Cache from './components-page/cache/view.jsx';
import Search from './components-page/search/view.jsx';
import Upload from './components-page/upload/view.jsx';
import ArticleEdit from './components-page/article_edit/view.jsx';
import ArticleDelete from './components-page/article_delete/view.jsx';
import QuestionEdit from './components-page/question_edit/view.jsx';
import QuestionDelete from './components-page/question_delete/view.jsx';
import AnswerEdit from './components-page/answer_edit/view.jsx';
import AnswerDelete from './components-page/answer_delete/view.jsx';
import CommentEdit from './components-page/comment_edit/view.jsx';
import CommentDelete from './components-page/comment_delete/view.jsx';

export default (state, actions) => ({ match }) => {
  const optionType = match.params.option;
  const { data, loading } = state;

  return (
    <div
      oncreate={(element) => actions.onCreate({ element })}
      ondestroy={(element) => actions.onDestroy({ element })}
      key={match.url}
      class="mdui-container"
      id="page-option"
    >
      <Nav path="/options" />

      <div class="mdui-card mdui-card-shadow option">
        <Loading show={loading} />
        <If condition={data}>
          <If condition={optionType === 'info'}>
            <Info state={state} actions={actions} />
          </If>
          <If condition={optionType === 'theme'}>
            <Theme state={state} actions={actions} />
          </If>
          <If condition={optionType === 'mail'}>
            <Mail state={state} actions={actions} />
          </If>
          <If condition={optionType === 'cache'}>
            <Cache state={state} actions={actions} />
          </If>
          <If condition={optionType === 'search'}>
            <Search state={state} actions={actions} />
          </If>
          <If condition={optionType === 'upload'}>
            <Upload state={state} actions={actions} />
          </If>
          <If condition={optionType === 'article_edit'}>
            <ArticleEdit state={state} actions={actions} />
          </If>
          <If condition={optionType === 'article_delete'}>
            <ArticleDelete state={state} actions={actions} />
          </If>
          <If condition={optionType === 'question_edit'}>
            <QuestionEdit state={state} actions={actions} />
          </If>
          <If condition={optionType === 'question_delete'}>
            <QuestionDelete state={state} actions={actions} />
          </If>
          <If condition={optionType === 'answer_edit'}>
            <AnswerEdit state={state} actions={actions} />
          </If>
          <If condition={optionType === 'answer_delete'}>
            <AnswerDelete state={state} actions={actions} />
          </If>
          <If condition={optionType === 'comment_edit'}>
            <CommentEdit state={state} actions={actions} />
          </If>
          <If condition={optionType === 'comment_delete'}>
            <CommentDelete state={state} actions={actions} />
          </If>
        </If>
      </div>
    </div>
  );
};
