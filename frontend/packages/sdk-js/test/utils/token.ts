import defaults from '../../es/defaults';
import { RequestAdapterInterface } from '../../es/util/misc';

const adapter = defaults.adapter as RequestAdapterInterface;

export const setManagerToken = (token: string): void =>
  adapter.setStorage('managerToken', token);

export const removeManagerToken = (): void =>
  adapter.removeStorage('managerToken');

export const setDefaultTokenToManager = (): string => {
  const token = adapter.getStorage('managerToken')!;
  adapter.setStorage('token', token);

  return token;
};

export const setNormalToken = (token: string): void =>
  adapter.setStorage('normalToken', token);

export const removeNormalToken = (): void =>
  adapter.removeStorage('normalToken');

export const setDefaultTokenToNormal = (): string => {
  const token = adapter.getStorage('normalToken')!;
  adapter.setStorage('token', token);

  return token;
};

export const removeDefaultToken = (): void => adapter.removeStorage('token');
