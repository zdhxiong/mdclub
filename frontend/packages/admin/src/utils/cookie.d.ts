/**
 * 设置 cookie，默认 15 天有效期
 * @param key
 * @param value
 * @param expire
 */
export declare function setCookie(key: string, value: string, expire: number): void;

/**
 * 删除 cookie
 * @param key
 */
export declare function removeCookie(key: string): void;
