import 'promise-polyfill/src/polyfill';
import 'mdn-polyfills/Object.assign';
import { encode } from 'qss';
import globalOptions from './defaults';

// 默认参数
const defaults = {
  method: 'GET',
  url: '',
  data: false,
};

// 判断是否支持 webp
const isSupportWebp = !![].map && document.createElement('canvas').toDataURL('image/webp').indexOf('data:image/webp') === 0;

// 触发回调函数
const triggerCallback = (callback, ...args) => {
  if (globalOptions[callback]) {
    globalOptions[callback](...args);
  }
};

// 发送请求
function request(opts) {
  const options = Object.assign({}, defaults, opts);

  const method = options.method.toUpperCase();
  let url = globalOptions.baseURL + options.url;
  let { data } = options;

  const headers = {
    Accept: `application/json, text/javascript${isSupportWebp ? ', image/webp' : ''}`,
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/json',
  };

  if (globalOptions.token) {
    headers.token = globalOptions.token;
  }

  if (data) {
    if (['GET', 'DELETE'].indexOf(method) > -1) {
      // GET 请求参数序列化
      if (typeof data !== 'string') {
        data = encode(data);
      }

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

  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true, '', '');

    Object.keys(headers).forEach((name) => {
      xhr.setRequestHeader(name, headers[name]);
    });

    xhr.onload = function () {
      let statusText = 'success';
      const { status } = xhr;

      if ((status >= 200 && status < 300) || status === 0) {
        let responseData;

        try {
          responseData = JSON.parse(xhr.responseText);
        } catch (err) {
          statusText = 'parsererror';
          triggerCallback('error', statusText);
        }

        if (statusText !== 'parseerror') {
          triggerCallback('success', responseData);

          if (responseData.code) {
            reject(responseData);
          } else {
            resolve(responseData);
          }
        }
      } else {
        statusText = 'error';
        triggerCallback('error', statusText);
      }

      triggerCallback('complete');
    };

    xhr.onerror = function () {
      const { statusText } = xhr;

      triggerCallback('error', statusText);
      triggerCallback('complete');
    };

    xhr.onabort = function () {
      const statusText = 'abort';

      triggerCallback('error', statusText);
      triggerCallback('complete');
    };

    triggerCallback('beforeSend');

    xhr.send(data);
  });
}

export default request;
