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
  const actions = global_actions.lazyComponents.userDialog;
  const state = global_state.lazyComponents.userDialog;

  return (
    <div class="mdui-dialog mc-user-dialog">
      {!state.loading && state.user ?
      <div
        class="header"
        oncreate={element => actions.headerInit(element)}
        style={{
          backgroundImage: `url(${state.user.cover.m})`,
        }}
      >
        <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient"></div>
        <div class="avatar">
          <img src={state.user.avatar.l}/>
        </div>
        <div class="username">{state.user.username} <small>(ID: {state.user.user_id})</small></div>
      </div> : ''}
      {!state.loading && state.user ?
      <div class="body">
        <div class="mdui-row">
          <div class="label mdui-text-color-theme-text">个人信息</div>
          <div class="card mdui-card">
            <TextItem subheader="邮箱" content={state.user.email}/>
            <TextItem subheader="一句话介绍" content={state.user.headline || '未填写'}/>
            <TextItem subheader="企业名称" content={state.user.company || '未填写'}/>
            <TextItem subheader="居住地" content={state.user.location || '未填写'}/>
            <TextItem subheader="个人主页" content={state.user.blog || '未填写'}/>
            <TextItem subheader="个人简介" content={state.user.bio || '未填写'}/>
          </div>
        </div>
        <div class="mdui-row">
          <div class="label mdui-text-color-theme-text">统计</div>
          <div class="card mdui-card card-with-count">
            <div class="text-item">
              <div class="text-subheader mdui-text-color-theme-secondary">发表的</div>
              <div class="text-content mdui-clearfix">
                <CountItem label="文章" count={state.user.article_count}/>
                <CountItem label="提问" count={state.user.question_count}/>
                <CountItem label="回答" count={state.user.answer_count}/>
              </div>
            </div>
            <div class="text-item">
              <div class="text-subheader mdui-text-color-theme-secondary">关注的</div>
              <div class="text-content mdui-clearfix">
                <CountItem label="文章" count={state.user.following_article_count}/>
                <CountItem label="提问" count={state.user.following_question_count}/>
                <CountItem label="话题" count={state.user.following_topic_count}/>
                <CountItem label="用户" count={state.user.following_user_count}/>
                <CountItem label="关注者" count={state.user.follower_count}/>
              </div>
            </div>
          </div>
        </div>
        <div class="mdui-row">
          <div class="label mdui-text-color-theme-text">注册登录信息</div>
          <div class="card mdui-card">
            <TextItem
              subheader="注册时间"
              content={timeHelper.friendly(state.user.create_time)}
              title={timeHelper.format(state.user.create_time)}
            />
            <TextItem
              subheader="注册IP"
              content={state.user.create_ip}
            />
            <TextItem
              subheader="最近登录时间"
              content={timeHelper.friendly(state.user.last_login_time)}
              title={timeHelper.format(state.user.last_login_time)}
            />
            <TextItem
              subheader="最近登录IP"
              content={state.user.last_login_ip}
            />
            <TextItem
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
