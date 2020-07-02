import { h } from 'hyperapp';
import { unescape } from 'html-escaper';
import './index.less';

import Loading from '~/components/loading/view.jsx';

const Textfield = ({ label, maxlength, name, value, type }) => (
  <div class="mdui-textfield mdui-textfield-floating-label">
    <label class="mdui-textfield-label">{label}</label>
    <If condition={type === 'text'}>
      <input
        class="mdui-textfield-input"
        name={name}
        type="text"
        autocomplete="off"
        maxlength={maxlength}
        value={unescape(value)}
      />
    </If>
    <If condition={type === 'textfield'}>
      <textarea
        class="mdui-textfield-input"
        name={name}
        autocomplete="off"
        maxlength={maxlength}
        value={unescape(value)}
      />
    </If>
  </div>
);

const Header = ({ state, onHeaderCreate, onDeleteAvatar, onDeleteCover }) => (
  <div
    class="header"
    oncreate={onHeaderCreate}
    style={{ backgroundImage: `url(${state.cover.middle})` }}
  >
    <button
      class="delete-cover mdui-btn mdui-btn-icon mdui-ripple"
      type="button"
      title="点击删除封面"
      onclick={onDeleteCover}
    >
      <i class="mdui-icon material-icons">delete</i>
    </button>
    <div class="gradient mdui-card-media-covered mdui-card-media-covered-gradient" />
    <div class="avatar">
      <img src={state.avatar.large} />
      <button
        class="delete-avatar mdui-btn mdui-btn-icon mdui-ripple"
        type="button"
        title="点击删除头像"
        onclick={onDeleteAvatar}
      >
        <i class="mdui-icon material-icons">delete</i>
      </button>
    </div>
    <div class="username">{state.username}</div>
  </div>
);

const Form = ({ state }) => (
  <form class="form">
    <Textfield
      label="一句话介绍"
      maxlength={40}
      name="headline"
      value={state.headline}
      type="text"
    />
    <Textfield
      label="个人主页"
      maxlength={80}
      name="blog"
      value={state.blog}
      type="text"
    />
    <Textfield
      label="所属学校或企业"
      maxlength={80}
      name="company"
      value={state.company}
      type="text"
    />
    <Textfield
      label="所在地区"
      maxlength={80}
      name="location"
      value={state.location}
      type="text"
    />
    <Textfield
      label="个人简介"
      maxlength={160}
      name="bio"
      value={state.bio}
      type="textfield"
    />
  </form>
);

const Actions = ({ onSubmit }) => (
  <div class="mdui-dialog-actions">
    <button class="mdui-btn mdui-ripple" mdui-dialog-close>
      取消
    </button>
    <button class="mdui-btn mdui-ripple" onclick={onSubmit}>
      确定
    </button>
  </div>
);

export default ({ state, actions }) => (
  <div
    class="mc-user-edit mdui-dialog"
    oncreate={(element) => actions.onCreate({ element })}
  >
    <div class="mdui-dialog-title">编辑用户信息</div>

    <Loading show={state.loading} />

    <If condition={!state.loading}>
      <div class="mdui-dialog-content">
        <Header
          state={state}
          onHeaderCreate={actions.onHeaderCreate}
          onDeleteAvatar={actions.deleteAvatar}
          onDeleteCover={actions.deleteCover}
        />
        <Form state={state} />
      </div>
    </If>

    <Actions onSubmit={actions.submit} />
  </div>
);
