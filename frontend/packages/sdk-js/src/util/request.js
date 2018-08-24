import globalOptions from './defaults';

// 判断是否支持 webp
const isSupportWebp = !![].map && document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0;

// 对象转为查询字符串
function param(obj) {
  const args = [];

  Object.keys(obj).forEach((key) => {
    let value = '';

    if (obj[key] !== null && obj[key] !== '') {
      value = `=${encodeURIComponent(obj[key])}`;
    }

    args.push(encodeURIComponent(key) + value);
  });

  return args.join('&');
}

// 扩展函数
function extend(obj, ...args) {
  const { length } = args;
  let i;
  let options;

  for (i = 0; i < length; i += 1) {
    options = args[i];

    /* eslint no-loop-func: "off" */
    Object.keys(options).forEach((prop) => {
      obj[prop] = options[prop];
    });
  }

  return obj;
}

// 发送请求
function request(opts) {
  // 默认参数
  const defaults = {
    method: 'GET',
    url: '',
    data: false,
    // beforeSend:    function (XMLHttpRequest) 请求发送前执行，返回 false 可取消本次 ajax 请求
    // success:       function (data, textStatus, XMLHttpRequest) 请求成功时调用
    // error:         function (XMLHttpRequest, textStatus) 请求失败时调用
    // complete:      function (XMLHttpRequest, textStatus) 请求完成后回调函数 (请求成功或失败之后均调用)
  };

  const options = extend({}, defaults, opts);
  let { data, url, method } = options;
  url = globalOptions.baseURL + url;
  method = method.toUpperCase();

  // 触发 xhr 回调
  function triggerCallback(callback, ...args) {
    // 全局回调
    if (globalOptions[callback]) {
      globalOptions[callback](...args);
    }

    // 自定义回调
    if (options[callback]) {
      options[callback](...args);
    }
  }

  // headers
  const headers = {
    Accept: `application/json, text/javascript${isSupportWebp ? ', image/webp' : ''}`,
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/json',
  };

  if (globalOptions.token) {
    headers.token = globalOptions.token;
  }

  // data
  if (data) {
    if (method === 'GET' && typeof data !== 'string') {
      // GET 请求参数序列化
      data = param(data);
    }

    if (method === 'GET') {
      // GET 请求，把 data 数据添加到 URL 中。URL 中不存在 ? 时，自动把第一个 & 替换为 ?
      url = `${url}&${data}`.replace(/[&?]{1,2}/, '?');
    }

    if (['POST', 'PATCH', 'PUT'].indexOf(method) > -1 && FormData !== data.constructor) {
      // JSON 数据转为字符串
      data = typeof data === 'string' ? data : JSON.stringify(data);
    }

    if (FormData === data.constructor) {
      delete headers['Content-Type'];
    }
  }

  const xhr = new XMLHttpRequest();
  xhr.open(method, url, true, '', '');

  Object.keys(headers).forEach((key) => {
    xhr.setRequestHeader(key, headers[key]);
  });

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

export default request;
