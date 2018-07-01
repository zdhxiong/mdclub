import $ from 'mdui.JQ';

export default {
  getAll(success) {
    $.ajax({
      method: 'get',
      url: `${window.G_API}/settings`,
      success,
    });
  },

  update(data, success) {
    $.ajax({
      method: 'patch',
      url: `${window.G_API}/settings`,
      data: typeof data === 'string' ? data : JSON.stringify(data),
      success,
    });
  },
};
