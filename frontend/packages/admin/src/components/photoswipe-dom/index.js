import { h } from 'hyperapp';
import './index.less';

/**
 * PhotoSwipe 插件的 DOM 部分
 */
export default () => (
  <div class="pswp" tabIndex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
      <div class="pswp__container">
        <div class="pswp__item"></div>
        <div class="pswp__item"></div>
        <div class="pswp__item"></div>
      </div>
      <div class="pswp__ui pswp__ui--hidden">
        <div class="pswp__top-bar">
          <div class="pswp__counter"></div>
          <button class="pswp__button pswp__button--close" title="关闭 (Esc)"></button>
          <button class="pswp__button pswp__button--fs" title="全屏切换"></button>
          <button class="pswp__button pswp__button--zoom" title="方法/缩小"></button>
          <div class="pswp__preloader">
            <div class="pswp__preloader__icn">
              <div class="pswp__preloader__cut">
                <div class="pswp__preloader__donut"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
          <div class="pswp__share-tooltip"></div>
        </div>
        <button class="pswp__button pswp__button--arrow--left" title="上一张 (向左键)"></button>
        <button class="pswp__button pswp__button--arrow--right" title="下一张 (向右键)"></button>
        <div class="pswp__caption">
          <div class="pswp__caption__center"></div>
        </div>
      </div>
    </div>
  </div>
);
