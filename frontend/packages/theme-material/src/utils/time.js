import timestamp from 'time-stamp';

/**
 * 把时间戳格式化
 * @param ts
 * @param format
 */
export function timeFormat(ts, format = 'YYYY-MM-DD HH:mm:ss') {
  const date = new Date(ts * 1000);

  return timestamp(format, date);
}

/**
 * 把时间转换为用户友好的显示方式
 * @param ts
 */
export function timeFriendly(ts) {
  // 时间对象
  const date = new Date(ts * 1000);

  // 当前时间戳
  const currentTS = Date.parse(new Date().toString()) / 1000;

  // 时间差
  const timeDifference = currentTS - ts;

  if (timeDifference < 1) {
    return '刚刚';
  }

  if (timeDifference < 60) {
    return `${timeDifference} 秒前`;
  }

  if (timeDifference < 3600) {
    return `${parseInt(String(timeDifference / 60), 10)} 分钟前`;
  }

  // 今天 0 点时间戳
  const todayTS =
    Date.parse(new Date(new Date().setHours(0, 0, 0, 0)).toString()) / 1000;

  if (ts > todayTS) {
    return `今天 ${timestamp('HH:mm', date)}`;
  }

  // 今年 0 点时间戳
  const yearTS =
    Date.parse(
      new Date(`${new Date().getFullYear()}/01/01 00:00:00`).toString(),
    ) / 1000;

  if (ts > yearTS) {
    return timestamp('MM-DD HH:mm', date);
  }

  return timestamp('YYYY-MM-DD', date);
}

/**
 * 获取当前时间戳
 */
export function currentTimestamp() {
  return Date.parse(new Date().toString()) / 1000;
}
