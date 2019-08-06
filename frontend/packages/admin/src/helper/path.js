/**
 * 为链接补充前缀
 * @param url
 * @returns {string}
 */
const addPrefix = url => window.G_ADMIN_ROOT + url;

/**
 * 判断当前路由是否和指定路径匹配
 * @param url
 * @param absolute 是否绝对匹配，若为 false，则只匹配前缀
 * @returns {boolean}
 */
const isMatched = (url, absolute = true) => {
  if (absolute) {
    return window.location.pathname === addPrefix(url);
  }

  return window.location.pathname.indexOf(addPrefix(url)) === 0;
};

export default {
  addPrefix,
  isMatched,
};
