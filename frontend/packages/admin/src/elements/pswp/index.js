import { h } from 'hyperapp';
import './index.less';

const Item = () => (
  <div class="pswp__item"></div>
);

const Button = ({ type, title }) => (
  <button class={`pswp__button pswp__button--${type}`} title={title}></button>
);

/**
 * PhotoSwipe 插件的 DOM 部分
 */
export default () => (
  <div
    class="pswp"
    tabIndex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
      <div class="pswp__container">
        <Item/>
        <Item/>
        <Item/>
      </div>
      <div class="pswp__ui pswp__ui--hidden">
        <div class="pswp__top-bar">
          <div class="pswp__counter"></div>
          <Button type="close" title="关闭 (Esc)"/>
          <Button type="fs" title="全屏切换"/>
          <Button type="zoom" title="放大/缩小"/>
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
        <Button type="arrow--left" title="上一张 (向左键)"/>
        <Button type="arrow--right" title="下一张 (向右键)"/>
        <div class="pswp__caption">
          <div class="pswp__caption__center"></div>
        </div>
      </div>
    </div>
  </div>
);
