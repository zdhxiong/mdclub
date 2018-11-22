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
    // 时间对象
    const date = new Date(ts * 1000);

    // 当前时间戳
    const currentTS = Date.parse(new Date()) / 1000;

    // 时间差
    const timeDifference = currentTS - ts;

    if (timeDifference < 60) {
      return `${timeDifference} 秒前`;
    }

    if (timeDifference < 3600) {
      return `${parseInt(timeDifference / 60, 10)} 分钟前`;
    }

    // 今天 0 点时间戳
    const todayTS = new Date(new Date().setHours(0, 0, 0, 0)) / 1000;

    if (ts > todayTS) {
      return `今天 ${timestamp('HH:mm', date)}`;
    }

    // 今年 0 点时间戳
    const yearTS = new Date(`${new Date().getFullYear()}/01/01 00:00:00`) / 1000;

    if (ts > yearTS) {
      return timestamp('MM-DD HH:mm', date);
    }

    return timestamp('YYYY-MM-DD', date);
  },
};
