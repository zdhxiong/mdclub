import { h } from 'hyperapp';
import './index.less';

import User from './components/user/view.jsx';
import EditInfo from './components/edit-info/view.jsx';
import Contexts from './components/contexts/view.jsx';

export default (state, actions) => ({ match }) => {
  // 当前访问的用户ID
  const interviewee_id = parseInt(match.params.user_id, 10);

  // 用户是否访问自己的主页
  const is_me = state.user && interviewee_id === state.user.user_id;

  return (
    <div
      oncreate={() => actions.onCreate({ interviewee_id })}
      ondestroy={actions.onDestroy}
      key={match.url}
      id="page-user"
      class="mdui-container"
    >
      <User state={state} actions={actions} is_me={is_me} />
      <Contexts state={state} actions={actions} />
      <If condition={state.user}>
        <EditInfo
          user={state.user}
          edit_info_submitting={state.edit_info_submitting}
        />
      </If>
    </div>
  );
};
