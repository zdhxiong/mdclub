import { h } from 'hyperapp';
import mdui from 'mdui';
import $ from 'mdui.jq';
import { unescape } from 'html-escaper';
import './index.less';

const Title = () => (
  <div class="mdui-dialog-title">
    <button class="mdui-btn mdui-btn-icon mdui-ripple close" mdui-dialog-close>
      <i class="mdui-icon material-icons">close</i>
    </button>
    编辑个人信息
  </div>
);

const Textfield = ({ label, maxlength, name, value, type }) => (
  <div
    class="mdui-textfield mdui-textfield-floating-label"
    onupdate={(element) => mdui.updateTextFields(element)}
  >
    <label class="mdui-textfield-label">{label}</label>
    <If condition={type === 'text'}>
      <input
        class="mdui-textfield-input"
        name={name}
        type="text"
        maxlength={maxlength}
        value={unescape(value)}
      />
    </If>
    <If condition={type === 'textfield'}>
      <textarea
        class="mdui-textfield-input"
        name={name}
        maxlength={maxlength}
        value={unescape(value)}
      />
    </If>
  </div>
);

const Content = ({ user }) => (
  <form class="mdui-dialog-content">
    <Textfield
      label="一句话介绍"
      maxlength={40}
      name="headline"
      value={user.headline}
      type="text"
    />
    <Textfield
      label="个人主页"
      maxlength={80}
      name="blog"
      value={user.blog}
      type="text"
    />
    <Textfield
      label="所属学校或企业"
      maxlength={80}
      name="company"
      value={user.company}
      type="text"
    />
    <Textfield
      label="所在地区"
      maxlength={80}
      name="location"
      value={user.location}
      type="text"
    />
    <Textfield
      label="个人简介"
      maxlength={160}
      name="bio"
      value={user.bio}
      type="textfield"
    />
  </form>
);

const Actions = ({ edit_info_submitting }) => (
  <div class="mdui-dialog-actions">
    <button class="mdui-btn" mdui-dialog-close>
      取消
    </button>
    <button
      class="mdui-btn"
      mdui-dialog-confirm
      disabled={edit_info_submitting}
    >
      {edit_info_submitting ? '保存中' : '确定'}
    </button>
  </div>
);

export default ({ user, edit_info_submitting }) => (
  <div
    class="mdui-dialog edit-info"
    oncreate={(element) => $(element).mutation()}
  >
    <Title />
    <Content user={user} />
    <Actions edit_info_submitting={edit_info_submitting} />
  </div>
);
