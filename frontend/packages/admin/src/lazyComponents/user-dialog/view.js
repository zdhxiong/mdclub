import { h } from 'hyperapp';
import cc from 'classcat';
import { location } from '@hyperapp/router';
import timeHelper from '../../helper/time';
import './index.less';

import Loading from '../../components/loading';

const Item = ({ subheader, content, title }) => (
  <div class="item">
    <div class="subheader">{subheader}</div>
    <div class="content" title={title}>{content}</div>
  </div>
);

export default () => (global_state, global_actions) => {
  const actions = global_actions.lazyComponents.userDialog;
  const state = global_state.lazyComponents.userDialog;

  return (
    <div class="mdui-dialog mc-user-dialog">
      {!state.loading && state.user ?
      <div
        class={cc([
          'header',
          {
            'fixed': state.headerFixed,
          },
        ])}
        oncreate={element => actions.headerInit(element)}
        style={{
          backgroundImage: `url(${state.user.cover.m})`,
        }}
      >
        <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient"></div>
        <div class="avatar">
          <img src={state.user.avatar.l}/>
        </div>
        <div class="username">{state.user.username}</div>
        <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close>
          <i class="mdui-icon material-icons">close</i>
        </button>
      </div> : ''}
      {!state.loading && state.user ? <div class="body">
        <div class="mdui-row">
          <div class="label">联系方式</div>
          <div class="card mdui-card">
            <i class="mdui-icon material-icons mdui-text-color-theme-icon mdui-m-r-1">email</i>
            <a href={`mailto:${state.user.email}`}>{state.user.email}</a>
          </div>
        </div>
        <div class="mdui-row">
          <div class="label">个人简介</div>
          <div class="card mdui-card">
            <Item subheader="一句话介绍" content={state.user.headline || '未填写'}/>
            <Item subheader="企业名称" content={state.user.company || '未填写'}/>
            <Item subheader="居住地" content={state.user.location || '未填写'}/>
            <Item subheader="个人主页" content={state.user.blog || '未填写'}/>
            <Item subheader="个人简介" content={state.user.bio || '未填写'}/>
          </div>
        </div>
        <div class="mdui-row">
          <div class="label">注册登录信息</div>
          <div class="card mdui-card">
            <Item
              subheader="注册时间"
              content={timeHelper.friendly(state.user.create_time)}
              title={timeHelper.format(state.user.create_time)}
            />
            <Item
              subheader="注册IP"
              content={state.user.create_ip}
            />
            <Item
              subheader="最近登录时间"
              content={timeHelper.friendly(state.user.last_login_time)}
              title={timeHelper.format(state.user.last_login_time)}
            />
            <Item
              subheader="最近登录IP"
              content={state.user.last_login_ip}
            />
            <Item
              subheader="账号信息更新时间"
              content={timeHelper.friendly(state.user.update_time)}
              title={timeHelper.format(state.user.update_time)}
            />
          </div>
        </div>
      </div> : ''}
      {state.loading ? <Loading/> : ''}
    </div>
  );

};
