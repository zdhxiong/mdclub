export default abstract class {
  /**
   * 获取数据存储
   * @param key
   */
  getStorage(key: string): string | null {
    return window.localStorage.getItem(key);
  }

  /**
   * 设置数据存储
   * @param key
   * @param data
   */
  setStorage(key: string, data: string): void {
    window.localStorage.setItem(key, data);
  }

  /**
   * 删除数据存储
   * @param key
   */
  removeStorage(key: string): void {
    window.localStorage.removeItem(key);
  }
}
