import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

import AvatarUpload from '../../components/avatar-upload';
import CoverUpload from '../../components/cover-upload';
import Loading from '../../components/loading';

export default (global_state, global_actions) => {
  const actions = global_actions.user;
  const state = global_state.user;

  return ({ match }) => {
    const user_id = parseInt(match.params.user_id, 10);

    // 用户是否访问自己的主页
    const is_me = user_id === state.user.user_id;
    const current_user = is_me ? state.user : state._user;

    return (
      <div
        oncreate={element => actions.init({ element, global_actions, user_id })}
        key={match.url}
        id="page-user"
        class="mdui-container"
      >
        {current_user.user_id ? <div
          class="mdui-card cover"
          oncreate={element => actions.coverInit(element)}
          style={{
            backgroundImage: `url(${current_user.cover.l})`,
          }}
        >
          {is_me ? <CoverUpload/> : ''}
          <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient"></div>
          <div class="info">
            <div class="avatar-box">
              {is_me ? <AvatarUpload/> : ''}
              <img src={current_user.avatar.l} class="avatar"/>
            </div>
            <div class="username">{current_user.username}</div>
            <div class="meta">
              <a
                href=""
                class="following"
                onclick={(e) => {
                  e.preventDefault();
                  global_actions.components.users_dialog.open({ type: 'following', id: current_user.user_id });
                }}
              >关注了 {current_user.following_count} 人</a>
              <span class="mdui-m-x-1">|</span>
              <a
                href=""
                class="followers"
                onclick={(e) => {
                  e.preventDefault();
                  global_actions.components.users_dialog.open({ type: 'followers', id: current_user.user_id });
                }}
              >{current_user.follower_count} 位关注者</a>
              {current_user.headline ? <span className="mdui-m-x-1">|</span> : ''}
              {current_user.headline ? <a href="" class="headline" onclick={(e) => { e.preventDefault(); }}>{current_user.headline}</a> : ''}
            </div>
            {is_me ?
              <div class="menu">
                <button
                  class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white mdui-text-color-white"
                  mdui-menu="{target: '#user-menu-menu', position: 'top', align: 'right'}"
                >
                  <i class="mdui-icon material-icons">more_vert</i>
                </button>
                <ul class="mdui-menu" id="user-menu-menu">
                  <li class="mdui-menu-item">
                    <a
                      onclick={actions.deleteAvatar}
                      href=""
                      class="mdui-ripple"
                    >删除头像</a>
                  </li>
                  <li class="mdui-menu-item">
                    <a
                      onclick={actions.deleteCover}
                      href=""
                      class="mdui-ripple"
                    >删除封面</a>
                  </li>
                </ul>
              </div> : ''}
            {is_me ?
              <button
                class="right-btn mdui-btn mdui-btn-raised"
                onclick={actions.updateHeadline}
              >修改个人简介</button> :
              <div
                onclick={actions.toggleFollow}
                class={cc([
                  'mdui-btn',
                  'mdui-btn-raised',
                  'right-btn',
                  {
                    following: state._user.relationship.is_following,
                  },
                ])}
              >{state._user.relationship.is_following ? '已关注' : '关注'}</div>}
          </div>
        </div> : ''}

        <Loading show={state.loading}/>

      </div>
    );
  };
};
