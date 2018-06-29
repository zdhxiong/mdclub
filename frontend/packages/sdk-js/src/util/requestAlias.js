import request from './request';

function responseHandle(response, success, error) {
  if (response.code) {
    error(response.code, response);
  } else {
    success(response.data, response.pagination);
  }
}

function requestHandle(method, url, ...args) {
  let [data, success, error] = args;

  if (typeof data === 'function') {
    error = success;
    success = data;
    data = false;
  }

  request({
    method,
    url,
    data,
    success: response => responseHandle(response, success, error),
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
