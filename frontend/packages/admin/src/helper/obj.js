export default {
  /**
   * 使用回调函数过滤对象
   * 依次将对象中的值传递到 callback 函数，如果函数返回 true，则该值会被包含在返回的对象中
   * 若没有指定回调函数，则将删除对象中所有等值为 false 的条目
   */
  filter(obj, callback = false) {
    Object.keys(obj).map((key) => {
      if (!callback && !obj[key]) {
        delete obj[key];
      } else if (typeof callback === 'function' && !callback(obj[key])) {
        delete obj[key];
      }
    });

    return obj;
  },
};
