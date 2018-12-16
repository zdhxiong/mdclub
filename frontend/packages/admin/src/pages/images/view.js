import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

import Loading from '../../components/loading';
import Empty from '../../components/empty';
import PhotoSwipeDom from '../../components/photoswipe-dom';
import Pagination from '../../lazyComponents/pagination/view';

export default (global_state, global_actions) => {
  const actions = global_actions.images;
  const state = global_state.images;

  const isEmpty = !state.loading && !state.data.length;
  const isLoading = state.loading;

  return ({ match }) => (
    <div
      oncreate={() => actions.init({ global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-images"
      class="mdui-container-fluid"
    >
      <div class="header">

      </div>
      <div class="list">
        <div class={cc([
          'mdui-grid-list',
          'mdui-clearfix',
          {
            'checked-all': state.isCheckedAll,
            checked: state.checkedCount,
          },
        ])}>
          <If condition={isLoading}><Loading/></If>
          <If condition={isEmpty}><Empty/></If>
          {state.data.map((item, index) => {
            const isChecked = state.isCheckedRows[item.hash];
            const thumbWidth = state.thumbData[index].width;
            const thumbHeight = state.thumbData[index].height;
            const thumbTransformX = 1 - (30 / state.thumbData[index].width);
            const thumbTransformY = 1 - (30 / state.thumbData[index].height);
            const thumbTransform = isChecked
              ? `translateZ(0px) scale3d(${thumbTransformX}, ${thumbTransformY}, 1)`
              : null;

            return (
              <div
                class={cc(['mdui-grid-tile', { checked: isChecked }])}
                style={{ height: `${thumbHeight}px`, width: `${thumbWidth}px` }}
              >
                <i
                  class="check-btn mdui-icon material-icons"
                  onclick={() => actions.checkOne(item.hash)}
                >check_circle</i>
                <i
                  class="check-placeholder-btn mdui-icon material-icons"
                  onclick={() => actions.checkOne(item.hash)}
                >radio_button_unchecked</i>
                <div
                  class="image"
                  style={{
                    backgroundImage: `url(${item.urls.r})`,
                    height: 0,
                    paddingBottom: `${thumbHeight}px`,
                    width: `${thumbWidth}px`,
                    transform: thumbTransform,
                  }}
                  onclick={e => actions.clickImage({ e, item, index })}
                >
                  <div
                    class="overlay-top mdui-grid-tile-actions mdui-grid-tile-actions-top mdui-grid-tile-actions-gradient"></div>
                  <div class="overlay-bottom mdui-grid-tile-actions mdui-grid-tile-actions-gradient">
                    <i
                      class="preview-btn mdui-icon material-icons"
                      onclick={() => actions.openImage({ item, index })}
                    >zoom_in</i>
                  </div>
                </div>
              </div>);
          })}
        </div>
      </div>
      <Pagination onChange={actions.loadData} loading={state.loading}/>
      <PhotoSwipeDom/>
    </div>
  );
};
