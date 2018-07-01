export default {
  _ua: navigator.userAgent,

  /**
   * 是否 Webkit
   * @returns {boolean}
   */
  isWebkit() {
    const reg = /webkit/i;
    return reg.test(this._ua);
  },

  /**
   * 是否 IE
   * @returns {boolean}
   */
  isIE() {
    return 'ActiveXObject' in window;
  },
};
