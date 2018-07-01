import request from './request';

function requestHandle(method, url, ...args) {
  let [data, success] = args;

  if (typeof data === 'function') {
    success = data;
    data = false;
  }

  request({
    method,
    url,
    data,
    success: response => success(response),
  });
}


function get(...args) {
  requestHandle('GET', ...args);
}

function post(...args) {
  requestHandle('POST', ...args);
}

function patch(...args) {
  requestHandle('PATCH', ...args);
}

function put(...args) {
  requestHandle('PUT', ...args);
}

function del(...args) {
  requestHandle('DELETE', ...args);
}

export {
  get,
  post,
  patch,
  put,
  del,
};
