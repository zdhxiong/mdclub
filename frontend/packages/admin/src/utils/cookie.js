/**
 * 设置 cookie，默认 15 天有效期
 * @param key
 * @param value
 * @param expire
 */
export function setCookie(key, value, expire = 15 * 24 * 3600) {
  const date = new Date();
  date.setTime(date.getTime() + expire * 1000);
  document.cookie = `${key}=${value}; expires=${date.toUTCString()}; path=/`;
}

/**
 * 删除 cookie
 * @param key
 */
export function removeCookie(key) {
  setCookie(key, '', -1);
}
