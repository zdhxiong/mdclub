export default {
  data: [], // 图片的原始信息数组
  thumbData: [], // 图片的缩略图尺寸数组
  photoSwipeItems: [], // PhotoSwipe 的数据数组
  loading: false,

  isCheckedRows: {}, // 键名为对象ID，键值为 bool 值
  isCheckedAll: false, // 是否已全部选中
  checkedCount: 0,
};
