import globalOptions from 'defaults';

// 判断是否支持 webp
const isSupportWebp = !![].map && document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0;

function request(opts) {
  // 默认参数
  const defaults = {
    method: 'GET',                   // 请求方式
    url: '',                         // 请求的 URL
    data: false,                     // 请求的数据，查询字符串或对象
    headers: {},                     // 一个键值对，随着请求一起发送
    contentType: 'application/json', // 发送信息至服务器时内容编码类型
  };

  if (globalOptions['headers']) {
    defaults['headers'] = globalOptions['headers'];
  }

  const options = Object.assign({}, defaults, opts);

  const method = options.method.toUpperCase();
  const contentType = options.contentType;
  const headers = options.headers;
  let data = options.data;
  let url = options.url;

  // 触发 xhr 回调
  function triggerCallback(callback) {
    if (options[callback]) {
      options[callback](arguments[1], arguments[2], arguments[3], arguments[4]);
    }
  }

  // 需要发送的数据
  if (method === 'GET' && data && FormData !== data.constructor && typeof data !== 'string') {
    data = JSON.stringify(data);
  }

  if (['POST', 'PATCH', 'PUT'].indexOf(method) > -1) {
    data = typeof data === 'string' ? data : JSON.stringify(data);
  }

  // 对于 GET、HEAD 类型的请求，把 data 数据添加到 URL 中
  if (method === 'GET' && data) {
    // 添加参数到 URL 上，且 URL 中不存在 ? 时，自动把第一个 & 替换为 ?
    url = (url + '&' + data).replace(/[&?]{1,2}/, '?');
    data = null;
  }

  const xhr = new XMLHttpRequest();
  xhr.open(method, url, true, '', '');

  headers['X-Requested-With'] = 'XMLHttpRequest';
  headers['Accept'] = `application/json, text/javascript${isSupportWebp ? ', image/webp' : ''}`;

  if (data && method !== 'GET' && contentType !== false || contentType) {
    headers['Content-Type'] = contentType;
  }

  // 添加 headers
  if (headers) {
    for (const key in headers) {
      xhr.setRequestHeader(key, headers[key]);
    }
  }

  xhr.onload = function () {
    let textStatus = 'success';

    if ((xhr.status >= 200 && xhr.status < 300) || xhr.status === 0) {
      let responseData;

      try {
        responseData = JSON.parse(xhr.responseText);
      } catch (err) {
        textStatus = 'parsererror';
        triggerCallback('error', xhr, textStatus);
      }

      if (textStatus !== 'parseerror') {
        triggerCallback('success', responseData, textStatus, xhr);
      }
    } else {
      textStatus = 'error';
      triggerCallback('error', xhr, textStatus);
    }

    triggerCallback('complete', xhr, textStatus);
  };

  xhr.onerror = function () {
    triggerCallback('error', xhr, xhr.statusText);
    triggerCallback('complete', xhr, xhr.statusText);
  };

  xhr.onabort = function () {
    triggerCallback('error', xhr, 'abort');
    triggerCallback('complete', xhr, 'abort');
  };

  triggerCallback('beforeSend', xhr);

  xhr.send(data);

  return xhr;
}

function get(url, data, success) {
  request({method: 'GET', url, data, success});
}

function post(url, data, success) {
  request({method: 'POST', url, data, success});
}

function patch(url, data, success) {
  request({method: 'PATCH', url, data, success});
}

function put(url, data, success) {
  request({method: 'put', url, data, success});
}

function del(url, data, success) {
  request({method: 'DELETE', url, data, success});
}

export { setup, request, get, post, patch, put, del };
