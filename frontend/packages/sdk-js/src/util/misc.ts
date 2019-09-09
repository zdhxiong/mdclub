export type BeforeSendCallback = () => void | false;
export type SuccessCallback = (response: any) => void;
export type ErrorCallback = (errMsg: string) => void;
export type CompleteCallback = () => void;

export interface PlainObject<T = any> {
  [key: string]: T;
}
