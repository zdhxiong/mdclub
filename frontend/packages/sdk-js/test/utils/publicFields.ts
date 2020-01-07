/**
 * 从对象中取出仅包含指定键名的数据
 * @param allData
 * @param publicKeys
 */
export default (allData: object, publicKeys: string[]): object => {
  const publicData = {};

  Object.keys(allData).forEach(key => {
    if (publicKeys.indexOf(key) > -1) {
      Object.assign(publicData, {
        // @ts-ignore
        [key]: allData[key],
      });
    }
  });

  return publicData;
};
