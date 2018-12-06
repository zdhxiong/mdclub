import { h } from 'hyperapp';
import './index.less';

export default () => (global_state, global_actions) => {
  const state = global_state.lazyComponents.searchBar;
  const actions = global_actions.lazyComponents.searchBar;

  return () => (
    <div
      class="mc-searchbar"
      oncreate={element => actions.init({ element, global_actions })}
    >
      <div class="bar">
        <i class="mdui-icon material-icons mdui-text-color-theme-icon">search</i>
        <span class="placeholder mdui-text-color-theme-icon">筛选条件</span>
        <i class="mdui-icon material-icons mdui-text-color-theme-icon">arrow_drop_down</i>
      </div>
      <form class="form mdui-menu">
        <div class="mdui-textfield">
          <label class="mdui-textfield-label">提问ID</label>
          <input class="mdui-textfield-input" type="text"/>
        </div>
        <div class="mdui-textfield">
          <label class="mdui-textfield-label">用户ID</label>
          <input class="mdui-textfield-input" type="text"/>
        </div>
        <select class="mdui-select" mdui-select>
          <option value="1">State 1</option>
          <option value="2">State 2</option>
          <option value="3" disabled>State 3</option>
          <option value="4">State 4</option>
          <option value="5">State 5</option>
          <option value="6">State 6</option>
        </select>
      </form>
    </div>
  );
};
