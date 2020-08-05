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
  const date = new Date(ts * 1000);
  const todayTimestamp = new Date(new Date().setHours(0, 0, 0, 0)) / 1000;
  const yearTimestamp =
    new Date(`${new Date().getFullYear()}/01/01 00:00:00`) / 1000;

  if (ts > todayTimestamp) {
    return `今天 ${timestamp('HH:mm', date)}`;
  }

  if (ts > yearTimestamp) {
    return timestamp('MM月DD日', date);
  }

  return timestamp('YYYY/MM/DD', date);
}

/**
 * 获取当前时间戳
 */
export function currentTimestamp() {
  return Date.parse(new Date().toString()) / 1000;
}
