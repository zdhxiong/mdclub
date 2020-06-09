import { h } from 'hyperapp';
import mdui from 'mdui';
import $ from 'mdui.jq';
import { COMMON_IMAGE_UPLOAD_FAILED } from 'mdclub-sdk-js/es/errors';
import { uploadMyAvatar } from 'mdclub-sdk-js/es/UserApi';
import { loadStart, loadEnd } from '~/utils/loading';
import { emit } from '~/utils/pubsub';
import apiCatch from '~/utils/errorHandler';
import './index.less';

const upload = (e, user) => {
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

  loadStart();

  uploadMyAvatar({ avatar: file })
    .finally(loadEnd)
    .then(({ data }) => {
      user.avatar = data;
      emit('user_update', user);
    })
    .catch((response) => {
      if (response.code === COMMON_IMAGE_UPLOAD_FAILED) {
        mdui.snackbar(response.extra_message);
        return;
      }

      apiCatch(response);
    });
};

export default ({ user }) => (
  <div class="mc-avatar-upload">
    <button
      onclick={(e) => {
        $(e.currentTarget).next().val('').trigger('click');
      }}
      class="upload-btn mdui-btn mdui-btn-icon mdui-ripple"
      type="button"
      title="点击上传头像"
    >
      <i class="mdui-icon material-icons">photo_camera</i>
    </button>
    <input
      onchange={(e) => upload(e, user)}
      type="file"
      title=" "
      accept="image/jpeg,image/png"
    />
  </div>
);
