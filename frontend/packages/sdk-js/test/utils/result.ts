export const success = (): void => {
  chai.assert.isOk(true);
};

export const failed = (message = ''): void => {
  chai.assert.isOk(false, message);
};

export const successWhen = (when: boolean): void => {
  chai.assert.isOk(when);
};
