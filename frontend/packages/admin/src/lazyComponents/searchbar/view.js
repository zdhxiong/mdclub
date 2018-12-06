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
        <div class="mdui-textfield">
          <label class="mdui-textfield-label">类型</label>
          <select class="mdui-select" mdui-select>
            <option value="article">文章</option>
            <option value="question">提问</option>
            <option value="answer">回答</option>
          </select>
        </div>
        <button class="submit mdui-btn mdui-btn-raised mdui-color-theme">搜索</button>
      </form>
    </div>
  );
};
