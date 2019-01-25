import timestamp from 'time-stamp';

export default {
  /**
   * 把时间戳格式化
   * @param ts
   * @param format
   * @returns {pattern}
   */
  format(ts, format = 'YYYY-MM-DD HH:mm:ss') {
    const date = new Date(ts * 1000);

    return timestamp(format, date);
  },

  /**
   * 把时间转换为用户友好的显示方式
   * @param ts
   * @returns {*}
   */
  friendly(ts) {
    const date = new Date(ts * 1000);
    const todayTimestamp = new Date(new Date().setHours(0, 0, 0, 0)) / 1000;
    const yearTimestamp = new Date(`${new Date().getFullYear()}/01/01 00:00:00`) / 1000;
    const hour = parseInt(timestamp('HH'), 10);

    if (ts > todayTimestamp) {
      return (hour < 12 ? '上午' : '下午') + timestamp('HH:mm', date);
    }

    if (ts > yearTimestamp) {
      return timestamp('MM月DD日', date);
    }

    return timestamp('YYYY/MM/DD', date);
  },
};
