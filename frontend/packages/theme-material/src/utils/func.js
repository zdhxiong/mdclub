import each from 'mdui.jq/es/functions/each';

export const findIndex = (items, prop, value) => {
  let result;

  each(items, (index, item) => {
    if (item[prop] === value) {
      result = index;
      return false;
    }

    return true;
  });

  return result;
};

export const findItem = (items, prop, value) => {
  const index = findIndex(items, prop, value);

  return items[index];
};
