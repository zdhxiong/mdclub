import request from './request';

function requestHandle(method, url, data = false) {
  return request({
    method,
    url,
    data,
  });
}

const get = (...args) => requestHandle('GET', ...args);
const post = (...args) => requestHandle('POST', ...args);
const patch = (...args) => requestHandle('PATCH', ...args);
const put = (...args) => requestHandle('PUT', ...args);
const del = (...args) => requestHandle('DELETE', ...args);

export {
  get,
  post,
  patch,
  put,
  del,
};
