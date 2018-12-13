import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import timeHelper from '../../helper/time';
import rawHtml from '../../helper/rawHtml';
import './index.less';

import Loading from '../../components/loading';

const ItemLabel = ({ name }) => (
  <div class="label mdui-text-color-theme-text">{name}</div>
);

const ItemText = ({ subheader, title, content, type = 'string' }) => (
  <div class="text-item">
    <div class="text-subheader mdui-text-color-theme-secondary">{subheader}</div>
    {type === 'html' ?
      <div
        class="text-content"
        title={title}
        oncreate={rawHtml(content)}
        onupdate={rawHtml(content)}
      ></div> :
      <div class="text-content" title={title}>{content}</div>
    }
  </div>
);

const ItemCount = ({ label, count }) => (
  <div class="count-item">
    <div class="count-label mdui-text-color-theme-secondary">{label}</div>
    <div class="count-number">{count}</div>
  </div>
);

export default () => (global_state, global_actions) => {
  const actions = global_actions.lazyComponents.dialogUser;
  const state = global_state.lazyComponents.dialogUser;

  return (
    <div class="mdui-dialog mc-dialog-user">
      {!state.loading && state.user ?
      <div
        class="header"
        oncreate={element => actions.headerInit(element)}
        style={{ backgroundImage: `url(${state.user.cover.m})` }}
      >
        <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient"></div>
        <div class="avatar">
          <img src={state.user.avatar.l}/>
        </div>
        <div class="username">{state.user.username}</div>
      </div> : ''}
      {!state.loading && state.user ?
      <div class="body">
        <div class="mdui-row">
          <div class="actions">
            <button class="mdui-fab mdui-color-blue-accent" mdui-tooltip="{content: '编辑账户信息'}">
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button class="mdui-fab mdui-color-pink-accent" mdui-tooltip="{content: '禁用账户'}">
              <i class="mdui-icon material-icons">lock</i>
            </button>
          </div>
        </div>
        <div class="mdui-row">
          <ItemLabel name="个人信息"/>
          <div class="card mdui-card">
            <ItemText subheader="账号ID" content={state.user.user_id}/>
            <ItemText subheader="邮箱" content={state.user.email}/>
            <ItemText subheader="一句话介绍" content={state.user.headline || '未填写'}/>
          </div>
        </div>
        <div class="mdui-row">
          <div class="card mdui-card">
            <ItemText subheader="企业名称" content={state.user.company || '未填写'}/>
            <ItemText subheader="居住地" content={state.user.location || '未填写'}/>
            <ItemText subheader="个人主页" content={state.user.blog || '未填写'}/>
            <ItemText subheader="个人简介" content={state.user.bio || '未填写'}/>
          </div>
        </div>
        <div class="mdui-row">
          <ItemLabel name="统计"/>
          <div class="card mdui-card card-with-count">
            <div class="text-item">
              <div class="text-subheader mdui-text-color-theme-secondary">发表的</div>
              <div class="text-content mdui-clearfix">
                <ItemCount label="文章" count={state.user.article_count}/>
                <ItemCount label="提问" count={state.user.question_count}/>
                <ItemCount label="回答" count={state.user.answer_count}/>
              </div>
            </div>
            <div class="text-item">
              <div class="text-subheader mdui-text-color-theme-secondary">关注的</div>
              <div class="text-content mdui-clearfix">
                <ItemCount label="文章" count={state.user.following_article_count}/>
                <ItemCount label="提问" count={state.user.following_question_count}/>
                <ItemCount label="话题" count={state.user.following_topic_count}/>
                <ItemCount label="用户" count={state.user.following_user_count}/>
                <ItemCount label="关注者" count={state.user.follower_count}/>
              </div>
            </div>
          </div>
        </div>
        <div class="mdui-row">
          <ItemLabel name="注册登录信息"/>
          <div class="card mdui-card">
            <ItemText
              subheader="注册时间"
              content={timeHelper.friendly(state.user.create_time)}
              title={timeHelper.format(state.user.create_time)}
            />
            <ItemText
              subheader="注册IP"
              content={`${state.user.create_ip} <small class="mdui-text-color-theme-secondary">( ${state.user.create_location} )</small>`}
              type="html"
            />
          </div>
        </div>
        <div class="mdui-row">
          <div class="card mdui-card">
            <ItemText
              subheader="最近登录时间"
              content={timeHelper.friendly(state.user.last_login_time)}
              title={timeHelper.format(state.user.last_login_time)}
            />
            <ItemText
              subheader="最近登录IP"
              content={`${state.user.last_login_ip} <small class="mdui-text-color-theme-secondary">( ${state.user.last_login_location} )</small>`}
              type="html"
            />
          </div>
        </div>
      </div> : ''}
      {state.loading && <Loading/>}
    </div>
  );
};
