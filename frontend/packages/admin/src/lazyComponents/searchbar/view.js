import { h } from 'hyperapp';
import cc from 'classcat';
import { JQ as $ } from 'mdui';
import './index.less';

export default () => (global_state, global_actions) => {
  const state = global_state.lazyComponents.searchBar;
  const actions = global_actions.lazyComponents.searchBar;

  return () => (
    <div
      class="mc-searchbar"
      oncreate={element => actions.init({ element, global_actions })}
    >
      <div class={cc([
        'bar',
        {
          'mdui-hidden': !state.isNeedRender,
        },
      ])}>
        <i class="mdui-icon material-icons mdui-text-color-theme-icon">search</i>
        <If condition={state.isDataEmpty}>
          <span class="placeholder mdui-text-color-theme-icon">筛选条件</span>
        </If>
        <If condition={!state.isDataEmpty}>
          <span class="chips">
            {state.fields.map((field) => {
              if (typeof state.data[field.name] !== 'undefined' && state.data[field.name] !== '') {
                let value;

                if (typeof field.enum !== 'undefined') {
                  field.enum.map((item) => {
                    if (state.data[field.name] === item.value) {
                      value = item.name;
                    }
                  });
                } else {
                  value = state.data[field.name];
                }

                return (
                  <div
                    class="mdui-chip"
                    key={field.name}
                    onclick={actions.onChipClick}
                  >
                    <span class="mdui-chip-title">{field.label}: {value}</span>
                    <span
                      class="mdui-chip-delete"
                      title="删除该条件"
                      onclick={() => actions.onChipDelete({ name: field.name })}
                    >
                      <i class="mdui-icon material-icons">cancel</i>
                    </span>
                  </div>);
              }

              return null;
            })}
          </span>
        </If>
        <i class="mdui-icon material-icons mdui-text-color-theme-icon">arrow_drop_down</i>
      </div>
      <form
        class={cc([
          'form',
          'mdui-menu',
          {
            'mdui-hidden': !state.isNeedRender,
          },
        ])}
        onsubmit={actions.onSubmit}
      >
        {state.fields.map((field) => {
          if (typeof field.enum === 'undefined') {
            return (
              <div class="mdui-textfield" key={field.name}>
                <label class="mdui-textfield-label">{field.label}</label>
                <input
                  class="mdui-textfield-input"
                  type="text"
                  autocomplete="off"
                  name={field.name}
                  value={state.data[field.name]}
                />
              </div>);
          }

          return (
            <div class="mdui-textfield" key={field.name}>
              <label class="mdui-textfield-label">{field.label}</label>
              <select
                class="mdui-select"
                name={field.name}
                mdui-select
                oncreate={element => $(element).mutation()}
              >
                {field.enum.map(option => (
                  <option
                    value={option.value}
                    selected={option.value === state.data[field.name]}
                  >{option.name}</option>
                ))}
              </select>
            </div>
          );
        })}
        <button
          class="submit mdui-btn mdui-btn-raised mdui-color-theme"
          type="submit"
        >搜索
        </button>
        <button
          class="reset mdui-btn"
          type="reset"
        >重置</button>
      </form>
    </div>
  );
};
