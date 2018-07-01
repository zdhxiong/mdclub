import $ from 'mdui.JQ';

export default {
  post: (success) => {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/captchas`,
      success,
    });
  },
};
