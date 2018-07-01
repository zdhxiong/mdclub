import { h } from 'hyperapp';
import mdui from 'mdui';
import $ from 'mdui.JQ';
import './index.less';
import UserAvatarService from '../../service/UserAvatar';

const upload = (e, global_actions) => {
  const file = e.target.files[0];

  if (file.size > 5 * 1024 * 1024) {
    mdui.snackbar('头像文件不能超过 5M');
    e.target.value = '';
    return;
  }

  if (['image/png', 'image/jpeg'].indexOf(file.type) < 0) {
    mdui.snackbar('只能上传 png、jpg 格式的图片');
    e.target.value = '';
    return;
  }

  $.loadStart();

  UserAvatarService.uploadMine(file, (response) => {
    $.loadEnd();

    if (!response.code) {
      global_actions.user.user.setAvatar(response.data);
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
  <div class="mc-avatar-upload">
    <button
      onclick={(e) => { $(e.currentTarget).next().trigger('click'); }}
      class="mdui-btn mdui-btn-icon mdui-ripple upload-btn"
      type="button"
      title="点击上传头像"
    >
      <i class="mdui-icon material-icons">photo_camera</i>
    </button>
    <input
      onchange={e => upload(e, global_actions)}
      type="file"
      title=" "
      accept="image/jpeg,image/png"
    />
  </div>
);
