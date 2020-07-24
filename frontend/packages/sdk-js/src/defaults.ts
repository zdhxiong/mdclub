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

  // 是否进行请求方法重写，若为true，则把 patch, put 请求重写成 post，把 delete 请求重写成 get
  methodOverride?: boolean;

  // 请求超时时间
  timeout?: number;

  // 适配器实例
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

const defaults: DefaultsInterface = {
  apiPath: '/api',
  methodOverride: false,
  timeout: 30000,
};

export default defaults;
