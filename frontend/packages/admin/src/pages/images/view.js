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
          'mdui-row-xs-2',
          'mdui-row-sm-3',
          'mdui-row-md-4',
          'mdui-row-lg-5',
          'mdui-row-xl-6',
          {
            'checked-all': state.isCheckedAll,
            checked: state.checkedCount,
          },
        ])}>
          <If condition={isLoading}><Loading/></If>
          <If condition={isEmpty}><Empty/></If>
          {state.data.map((item, index) => (
            <div class="mdui-col">
              <div
                class={cc([
                  'mdui-grid-tile',
                  { checked: state.isCheckedRows[item.hash] },
                ])}
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
                  style={`background-image: url('${item.urls.r}')`}
                  onclick={e => actions.clickImage({ e, item, index })}
                >
                  <div class="overlay-top mdui-grid-tile-actions mdui-grid-tile-actions-top mdui-grid-tile-actions-gradient"></div>
                  <div class="overlay-bottom mdui-grid-tile-actions mdui-grid-tile-actions-gradient">
                    <i
                      class="preview-btn mdui-icon material-icons"
                      onclick={() => actions.openImage({ item, index })}
                    >zoom_in</i>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
      <Pagination onChange={actions.loadData} loading={state.loading}/>
      <PhotoSwipeDom/>
    </div>
  );
};
