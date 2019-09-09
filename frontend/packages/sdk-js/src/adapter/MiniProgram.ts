import {
  RequestAdapterInterface,
  RequestOptionsInterface,
  ResponseInterface,
} from './AdapterInterface';
import globalOptions from '../defaults';
import { PlainObject } from '../util/misc';
import { isUndefined } from "../../types/mdui.jq/src/utils";

// eslint-disable-next-line @typescript-eslint/no-namespace
declare namespace wx {
  // #region 基本参数
  interface DataResponse {
    /** 回调函数返回的内容 */
    data: object | string | ArrayBuffer;
    /** 开发者服务器返回的 HTTP 状态码 */
    statusCode: number;
    /** 开发者服务器返回的 HTTP Response Header */
    header: object;
  }

  interface BaseOptions<R = any, E = any> {
    /** 接口调用成功的回调函数 */
    success?(res: R): void;
    /** 接口调用失败的回调函数 */
    fail?(res: E): void;
    /** 接口调用结束的回调函数（调用成功、失败都会执行） */
    complete?(res: any): void;
  }

  // #endregion
  // #region 网络API列表
  // 发起请求
  interface RequestHeader {
    [key: string]: string;
  }

  interface RequestOptions extends BaseOptions<DataResponse> {
    /** 开发者服务器接口地址 */
    url: string;
    /** 请求的参数 */
    data?: string | PlainObject | ArrayBuffer;
    /** 设置请求的 header , header 中不能设置 Referer */
    header?: RequestHeader;
    /** 默认为 GET，有效值：OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT */
    method?:
      | 'GET'
      | 'OPTIONS'
      | 'HEAD'
      | 'POST'
      | 'PUT'
      | 'DELETE'
      | 'TRACE'
      | 'CONNECT';
    /** 如果设为json，会尝试对返回的数据做一次 JSON.parse */
    dataType?: string;
    /**
     * 设置响应的数据类型。合法值：text、arraybuffer
     * @version 1.7.0
     */
    responseType?: string;
    /** 收到开发者服务成功返回的回调函数，res = {data: '开发者服务器返回的内容'} */
    success?(res: DataResponse): void;
  }

  // 上传下载
  interface UploadFileOptions extends BaseOptions {
    /** 开发者服务器 url */
    url: string;
    /** 要上传文件资源的路径 */
    filePath: string;
    /** 文件对应的 key , 开发者在服务器端通过这个 key 可以获取到文件二进制内容 */
    name: string;
    /** HTTP 请求 Header , header 中不能设置 Referer */
    header?: RequestHeader;
    /** HTTP 请求中其他额外的 form data */
    formData?: any;
  }

  /**
   * wx.request发起的是https请求。一个微信小程序，同时只能有5个网络请求连接。
   */
  function request(options: RequestOptions): RequestTask;

  /**
   * 将本地资源上传到开发者服务器。
   * 如页面通过 wx.chooseImage 等接口获取到一个本地资源的临时文件路径后，
   * 可通过此接口将本地资源上传到指定服务器。
   * 客户端发起一个 HTTPS POST 请求，
   * 其中 Content-Type 为 multipart/form-data 。
   */
  function uploadFile(options: UploadFileOptions): UploadTask;

  /**
   * 返回一个 requestTask 对象，通过 requestTask，可中断请求任务。
   */
  interface RequestTask {
    abort(): void;
  }

  interface UploadTask {
    /**
     * 监听上传进度变化
     * @version 1.4.0
     */
    onProgressUpdate(
      callback?: (
        res: {
          /** 上传进度百分比 */
          progress: number;
          /** 已经上传的数据长度，单位 Bytes */
          totalBytesSent: number;
          /** 预期需要上传的数据总长度，单位 Bytes */
          totalBytesExpectedToSend: number;
        }
      ) => void
    ): void;
    /**
     * 中断下载任务
     * @version 1.4.0
     */
    abort(): void;
  }

  /**
   * 将 data 存储在本地缓存中指定的 key 中，
   * 会覆盖掉原来该 key 对应的内容，这是一个同步接口。
   *
   * @param key 本地缓存中的指定的 key
   * @param data 需要存储的内容
   */
  function setStorageSync(key: string, data: any | string): void;

  /**
   * 从本地缓存中同步获取指定 key 对应的内容。
   *
   */
  function getStorageSync(key: string): any | string;
}

/**
 * 小程序适配器
 *
 * 支持 微信小程序、支付宝小程序、钉钉小程序、百度小程序
 */
export default class MiniProgram implements RequestAdapterInterface {
  /**
   * 获取数据存储
   * @param key
   */
  getStorage(key: string): string | null {
    return wx.getStorageSync(key);
  }

  /**
   * 设置数据存储
   * @param key
   * @param data
   */
  setStorage(key: string, data: string): void {
    wx.setStorageSync(key, data);
  }

  /**
   * 发送请求
   * @param options
   */
  request(options: RequestOptionsInterface): Promise<ResponseInterface> {
    return new Promise((resolve, reject): void => {
      // 设置 header
      const header: wx.RequestHeader = {
        'Content-Type': 'application/json',
      };

      const token = this.getStorage('token');
      if (token) {
        header.token = token;
      }

      // 小程序不支持 patch 请求
      if (options.method === 'PATCH') {
        options.method = 'POST';
        header['X-Http-Method-Override'] = 'PATCH';
      }

      // beforeSend 返回 false 时，取消请求
      if (globalOptions.beforeSend) {
        if (globalOptions.beforeSend() === false) {
          reject('cancel');
          return;
        }
      }

      // request 或 uploadFile 方法的参数
      const url = (globalOptions.apiPath || '') + (options.url || '');
      const data = options.data;

      const success = (res: wx.DataResponse): void => {
        globalOptions.success && globalOptions.success(res.data);
        resolve(res.data as ResponseInterface);
      };

      const fail = (res: any): void => {
        globalOptions.error && globalOptions.error(res);
        reject(res);
      };

      const complete = (): void => {
        globalOptions.complete && globalOptions.complete();
      };

      if (!isUndefined(options.data) && options.data instanceof FormData) {
        wx.uploadFile({
          url,
          filePath: '',
          name: '',
          formData: data,
        })
      } else {
        wx.request({
          method: options.method || 'GET',
          url,
          data,
          header,
          success,
          fail,
          complete,
        });
      }
    });
  }
}
