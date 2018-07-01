import $ from 'mdui.JQ';

export default {
  post: (data, success) => {
    $.ajax({
      method: 'post',
      url: `${window.G_API}/tokens`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },
};
