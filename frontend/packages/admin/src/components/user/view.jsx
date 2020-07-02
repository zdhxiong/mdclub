import { h } from 'hyperapp';
import cc from 'classcat';
import { richText, summaryText, plainText } from '~/utils/html';
import { timeFriendly, timeFormat } from '~/utils/time';
import './index.less';

import Loading from '~/components/loading/view.jsx';

const ItemLabel = ({ name }) => (
  <div class="label mdui-text-color-theme-text">{name}</div>
);

const ItemText = ({
  subheader,
  title,
  content,
  type = 'string',
  multiLine = false,
}) => {
  let contentFunc;

  if (multiLine) {
    contentFunc = plainText(content);
  } else {
    contentFunc = type === 'html' ? richText(content) : summaryText(content);
  }

  return (
    <div class="text-item">
      <div class="text-subheader mdui-text-color-theme-secondary">
        {subheader}
      </div>
      <div
        class={cc(['text-content', { 'mdui-typo': multiLine }])}
        title={title}
        oncreate={contentFunc}
        onupdate={contentFunc}
      />
    </div>
  );
};

const ItemCount = ({ label, count }) => (
  <div class="count-item">
    <div class="count-label mdui-text-color-theme-secondary">{label}</div>
    <div class="count-number">{count}</div>
  </div>
);

export default ({ state, actions }) => {
  const { loading, user } = state;

  return (
    <div class="mc-user mdui-dialog" oncreate={actions.onCreate}>
      <Loading show={loading} />

      <If condition={user}>
        <div
          class="header"
          oncreate={actions.onHeaderCreate}
          style={{ backgroundImage: `url(${user.cover.middle})` }}
        >
          <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient" />
          <div class="avatar">
            <img src={user.avatar.large} />
          </div>
          <div class="username">{user.username}</div>
        </div>
        <div class="body">
          <div class="actions">
            <button
              class="mdui-fab edit"
              mdui-tooltip="{content: '编辑账户信息', delay: 300}"
              onclick={actions.toEdit}
            >
              <i class="mdui-icon material-icons">edit</i>
            </button>
            <button
              class="mdui-fab disable"
              mdui-tooltip="{content: '禁用账户', delay: 300}"
              onclick={actions.disable}
            >
              <i class="mdui-icon material-icons">lock</i>
            </button>
          </div>
          <div class="section">
            <ItemLabel name="个人信息" />
            <div class="card mdui-card mdui-card-shadow">
              <ItemText subheader="账号ID" content={user.user_id.toString()} />
              <ItemText subheader="邮箱" content={user.email} />
              <ItemText
                subheader="一句话介绍"
                content={user.headline || '未填写'}
              />
            </div>
          </div>
          <div class="section">
            <div class="card mdui-card mdui-card-shadow">
              <ItemText
                subheader="学校或企业名称"
                content={user.company || '未填写'}
              />
              <ItemText
                subheader="居住地"
                content={user.location || '未填写'}
              />
              <ItemText subheader="个人主页" content={user.blog || '未填写'} />
              <ItemText
                subheader="个人简介"
                content={user.bio || '未填写'}
                multiLine={true}
              />
            </div>
          </div>
          <div class="section">
            <ItemLabel name="统计" />
            <div class="card mdui-card mdui-card-shadow card-with-count">
              <div class="text-item">
                <div class="text-subheader mdui-text-color-theme-secondary">
                  发表的
                </div>
                <div class="text-content mdui-clearfix">
                  <ItemCount label="文章" count={user.article_count} />
                  <ItemCount label="提问" count={user.question_count} />
                  <ItemCount label="回答" count={user.answer_count} />
                </div>
              </div>
              <div class="text-item">
                <div class="text-subheader mdui-text-color-theme-secondary">
                  关注的
                </div>
                <div class="text-content mdui-clearfix">
                  <ItemCount
                    label="文章"
                    count={user.following_article_count}
                  />
                  <ItemCount
                    label="提问"
                    count={user.following_question_count}
                  />
                  <ItemCount label="话题" count={user.following_topic_count} />
                  <ItemCount label="用户" count={user.followee_count} />
                  <ItemCount label="关注者" count={user.follower_count} />
                </div>
              </div>
            </div>
          </div>
          <div class="section">
            <ItemLabel name="注册登录信息" />
            <div class="card mdui-card mdui-card-shadow">
              <ItemText
                subheader="注册时间"
                content={timeFriendly(user.create_time)}
                title={timeFormat(user.create_time)}
              />
              <ItemText
                subheader="注册IP"
                content={`${user.create_ip} <small class="mdui-text-color-theme-secondary">( ${user.create_location} )</small>`}
                type="html"
              />
            </div>
          </div>
          <div class="section">
            <div class="card mdui-card mdui-card-shadow">
              <ItemText
                subheader="最近登录时间"
                content={timeFriendly(user.last_login_time)}
                title={timeFormat(user.last_login_time)}
              />
              <ItemText
                subheader="最近登录IP"
                content={`${user.last_login_ip} <small class="mdui-text-color-theme-secondary">( ${user.last_login_location} )</small>`}
                type="html"
              />
            </div>
          </div>
        </div>
      </If>
    </div>
  );
};
