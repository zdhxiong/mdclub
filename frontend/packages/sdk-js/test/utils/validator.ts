import errors from './errors';
import { removeDefaultToken, setDefaultTokenToNormal } from './token';

// @ts-ignore
import Validator from 'swagger-model-validator';
// @ts-ignore
import openapi from 'mdclub-openapi/dist/openapi.json';
import { failed, success, successWhen } from './result';

const validator = new Validator(openapi);

interface Error {
  name: string;
  message: string;
}

interface ValidatorResult {
  valid: boolean;
  errorCount: number;
  errors?: Error[];
}

export const needLogin = (method: any, params: any = {}): Promise<any> => {
  removeDefaultToken();

  return method(params)
    .then(() => failed('未登录用户不允许该操作'))
    .catch((response: any) =>
      successWhen(response.code === errors.USER_NEED_LOGIN),
    );
};

export const needManager = (method: any, params: any = {}): Promise<any> => {
  setDefaultTokenToNormal();

  return method(params)
    .then(() => failed('普通用户不允许该操作'))
    .catch((response: any) =>
      successWhen(response.code === errors.USER_NEED_MANAGE_PERMISSION),
    );
};

export const deepEqual = (data: any, target: any): void => {
  chai.assert.deepEqual(data, target);
};

export const lengthOf = (data: any, target: number): void => {
  chai.assert.lengthOf(data, target);
};

export const include = (data: any, target: string | string[]): void => {
  if (!Array.isArray(target)) {
    target = [target];
  }

  target.forEach(item => chai.assert.include(Object.keys(data), item));
};

export const notInclude = (data: any, target: string | string[]): void => {
  if (!Array.isArray(target)) {
    target = [target];
  }

  target.forEach(item => chai.assert.notInclude(Object.keys(data), item));
};

/**
 * 验证数据和模型匹配
 * @param target 需要验证的数据
 * @param model  openapi 中的 schema 对象
 */
export const matchModel = (target: object, model: object): void => {
  const result: ValidatorResult = validator.validate(
    target,
    model,
    openapi.components.schemas,
    false,
    true,
  );

  result.valid
    ? success()
    : failed(result.errors!.map(error => error.message).join(', '));
};
