import { h } from 'hyperapp';
import mdui, { JQ as $ } from 'mdui';
import { User } from 'mdclub-sdk-js';
import './index.less';

const upload = (e, global_actions) => {
  const file = e.target.files[0];

  if (file.size > 5 * 1024 * 1024) {
    mdui.snackbar('封面文件不能超过 5M');
    e.target.value = '';
    return;
  }

  if (['image/png', 'image/jpeg'].indexOf(file.type) < 0) {
    mdui.snackbar('只能上传 png、jpg 格式的图片');
    e.target.value = '';
    return;
  }

  $.loadStart();

  User.uploadMyCover(file, (response) => {
    $.loadEnd();

    if (!response.code) {
      global_actions.user.user.setCover(response.data);
      return;
    }

    if (response.code === 200006) {
      mdui.snackbar(response.extra_message);
      return;
    }

    mdui.snackbar(response.message);
  });
};

export default () => (global_state, global_actions) => (
  <div class="mc-cover-upload">
    <button
      onclick={(e) => { $(e.currentTarget).next().trigger('click'); }}
      class="mdui-btn mdui-btn-icon mdui-ripple upload-btn"
      type="button"
      title="点击上传封面"
    >
      <i className="mdui-icon material-icons">photo_camera</i>
    </button>
    <input
      onchange={e => upload(e, global_actions)}
      type="file"
      title=" "
      accept="image/jpeg,image/png"
    />
  </div>
);
