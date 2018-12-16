import { JQ as $ } from 'mdui';

/**
 * 根据当前屏幕宽度，计算每个图片的缩略图的长宽
 * 每行图片高度固定，宽度自适应
 */
const resizeImage = (items) => {
  const minHeight = 172;
  const maxHeight = 272;
  const wrapperWidth = $('#page-images .mdui-grid-list').width();
  const thumbData = [];

  // 根据图片的最小高度算出图片宽度
  const widths = [];
  items.map((item) => {
    widths.push(Math.round((item.width / item.height) * minHeight));
  });

  let rows = [];
  let wrapperWidthRemaining = wrapperWidth;

  items.map((item, index) => {
    const nextWidth = typeof widths[index + 1] === 'undefined' ? 0 : widths[index + 1];
    wrapperWidthRemaining -= widths[index];
    rows[index] = item;

    // 下一张图片在当前行放不下，或已经没有下一张图片
    if (!nextWidth || wrapperWidthRemaining - nextWidth < 0) {
      let totalWidth = 0;
      rows.map((_item, _index) => {
        totalWidth += (widths[_index] + 4);
      });

      rows.map((_item, _index) => {
        let height = minHeight * (wrapperWidth / totalWidth);
        let width = widths[_index] * (wrapperWidth / totalWidth);

        if (height > maxHeight) {
          width *= (maxHeight / height);
          height = maxHeight;
        }

        thumbData.push({
          width: Math.floor(width),
          height: Math.floor(height),
        });
      });

      rows = [];
      wrapperWidthRemaining = wrapperWidth;
    }
  });

  return thumbData;
};

export {
  resizeImage,
};
