import { PlainObject } from '../util/misc';
import { Pagination } from '../models';

/**
 * 请求的响应数据接口
 */
export interface ResponseInterface {
  code: number;
  data: any;
  pagination: Pagination;
}

/**
 * 请求适配器参数接口
 */
export interface RequestOptionsInterface {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
  url?: string;
  data?: PlainObject | FormData;
}

/**
 * 请求适配器接口
 */
export interface RequestAdapterInterface {
  /**
   * 获取数据存储
   * @param key
   */
  getStorage(key: string): string | null;

  /**
   * 设置数据存储
   * @param key
   * @param data
   */
  setStorage(key: string, data: string): void;

  /**
   * 删除数据存储
   * @param key
   */
  removeStorage(key: string): void;

  /**
   * 发送请求
   * @param options
   */
  request(options: RequestOptionsInterface): Promise<ResponseInterface>;
}
