import { h } from 'hyperapp';
import './index.less';

const Spacer = () => (
  <div class="mdui-toolbar-spacer"></div>
);

const PerPage = ({ onChange, perPage, loading }) => (
  <div class="per-page">
    <span class="label mdui-typo-caption">每页行数：</span>
    <select class="mdui-select" onchange={onChange} disabled={loading}>
      <option value="10" selected={perPage === 10}>10</option>
      <option value="25" selected={perPage === 25}>25</option>
      <option value="50" selected={perPage === 50}>50</option>
      <option value="100" selected={perPage === 100}>100</option>
    </select>
  </div>
);

const Page = ({ onSubmit, page, pages, loading }) => (
  <div class="page mdui-typo-caption">
    第
    <form class="form" onsubmit={onSubmit}>
      <input
        class="input mdui-textfield-input"
        type="text"
        name="page"
        autoComplete="off"
        disabled={loading}
        value={page}
      />
    </form>
    页，共 {pages} 页
  </div>
);

const PrevPage = ({ onClick, previous, loading }) => (
  <button
    class="prev mdui-btn mdui-btn-icon"
    title="上一页"
    disabled={!previous || loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_left</i>
  </button>
);

const NextPage = ({ onClick, next, loading }) => (
  <button
    class="next mdui-btn mdui-btn-icon"
    title="下一页"
    disabled={!next || loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_right</i>
  </button>
);

export default ({ onPaginationChange }) => (global_state, global_actions) => {
  const state = global_state.lazyComponents.pagination;
  const actions = global_actions.lazyComponents.pagination;

  return () => (
    <div class="mc-pagination mdui-toolbar" oncreate={element => actions.init({ element })}>
      <Spacer/>
      <PerPage
        onChange={e => actions.onPerPageChange({ e, onPaginationChange })}
        perPage={state.per_page}
        loading={state.loading}
      />
      <Page
        onSubmit={e => actions.onPageChange({ e, onPaginationChange })}
        page={state.page}
        pages={state.pages}
        loading={state.loading}
      />
      <PrevPage
        onClick={() => actions.toPrevPage(onPaginationChange)}
        previous={state.previous}
        loading={state.loading}
      />
      <NextPage
        onClick={() => actions.toNextPage(onPaginationChange)}
        next={state.next}
        loading={state.loading}
      />
    </div>
  );
};
