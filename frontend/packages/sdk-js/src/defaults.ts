import {
  BeforeSendCallback,
  SuccessCallback,
  ErrorCallback,
  CompleteCallback,
  RequestAdapterInterface,
} from './util/misc';

/**
 * SDK 全局配置参数
 */
interface DefaultsInterface {
  // API 地址
  apiPath?: string;

  /**
   * 适配器实例
   */
  adapter?: RequestAdapterInterface;

  // 开始发送请求前的回调函数，返回 false 可取消请求
  beforeSend?: BeforeSendCallback;

  // 请求成功的回调函数
  success?: SuccessCallback;

  // 请求失败的回调函数
  error?: ErrorCallback;

  // 请求完成的回调函数
  complete?: CompleteCallback;
}

const defaults: DefaultsInterface = {};

export default defaults;
