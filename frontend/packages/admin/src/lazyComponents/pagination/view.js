import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import './index.less';

const Spacer = () => (
  <div class="mdui-toolbar-spacer"></div>
);

const PerPage = ({ state, loading, onChange }) => (
  <div class="per-page">
    <span class="label mdui-typo-caption">每页行数：</span>
    <select
      mdui-select
      class="mdui-select"
      disabled={loading}
      onchange={onChange}
      oncreate={element => $(element).mutation()}
    >
      <option value="10" selected={state.per_page === 10}>10</option>
      <option value="20" selected={state.per_page === 20}>20</option>
      <option value="40" selected={state.per_page === 40}>40</option>
      <option value="100" selected={state.per_page === 100}>100</option>
    </select>
  </div>
);

const Page = ({ state, loading, onSubmit }) => (
  <div class="page mdui-typo-caption">
    第
    <form class="form" onsubmit={onSubmit}>
      <input
        class="input mdui-textfield-input"
        type="text"
        name="page"
        autoComplete="off"
        disabled={loading}
        value={state.page}
      />
    </form>
    页，共 {state.pages} 页
  </div>
);

const PrevPage = ({ state, loading, onClick }) => (
  <button
    class="prev mdui-btn mdui-btn-icon"
    title="上一页"
    disabled={!state.previous || loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_left</i>
  </button>
);

const NextPage = ({ state, loading, onClick }) => (
  <button
    class="next mdui-btn mdui-btn-icon"
    title="下一页"
    disabled={!state.next || loading}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons">chevron_right</i>
  </button>
);

export default ({ onChange, loading }) => (global_state, global_actions) => {
  const state = global_state.lazyComponents.pagination;
  const actions = global_actions.lazyComponents.pagination;

  return () => (
    <div
      class="mc-pagination mdui-toolbar"
      ondestroy={actions.destroy}
    >
      <Spacer/>
      <PerPage
        state={state}
        loading={loading}
        onChange={e => actions.onPerPageChange({ e, onChange })}
      />
      <Page
        state={state}
        loading={loading}
        onSubmit={e => actions.onPageChange({ e, onChange })}
      />
      <PrevPage
        state={state}
        loading={loading}
        onClick={() => actions.toPrevPage(onChange)}
      />
      <NextPage
        state={state}
        loading={loading}
        onClick={() => actions.toNextPage(onChange)}
      />
    </div>
  );
};
