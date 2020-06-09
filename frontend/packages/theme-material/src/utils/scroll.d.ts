/**
 * 页面滚动到顶部
 * @param duration 滚动持续时间，默认为 300ms
 */
export declare function scrollToTop(duration: number): void;

type ScrollHorizontalOptions = {
  /**
   * 滚动持续时间，默认为 300ms
   */
  duration: number;

  /**
   * 滚动距离
   */
  offset: number;

  /**
   * 滚动结束回调
   */
  callback: () => void;
};

/**
 * 元素滚动
 * @param element
 * @param options
 */
export declare function scrollHorizontal(element: HTMLElement, options: ScrollHorizontalOptions): void;
