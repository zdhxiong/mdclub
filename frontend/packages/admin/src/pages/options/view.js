import { h } from 'hyperapp';
import './index.less';

export default (global_state, global_actions) => {
  const actions = global_actions.options;
  const state = global_state.options;

  return ({ match }) => (
    <div
      oncreate={element => actions.init({ element, global_actions })}
      ondestroy={actions.destroy}
      key={match.url}
      id="page-options"
      class="mdui-container"
    >
      <div class="mdui-panel">

        <div class="mdui-panel-item">
          <div class="mdui-panel-item-header">
            <div class="mdui-panel-item-title">站点信息</div>
            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
          </div>
          <div class="mdui-panel-item-body">
            <p>First content</p>
            <p>First content</p>
            <p>First content</p>
            <p>First content</p>
            <p>First content</p>
            <p>First content</p>
          </div>
        </div>

        <div class="mdui-panel-item">
          <div class="mdui-panel-item-header">
            <div class="mdui-panel-item-title">主题</div>
            <div class="mdui-panel-item-summary">material</div>
            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
          </div>
          <div class="mdui-panel-item-body">
            <p>Second content</p>
            <p>Second content</p>
            <p>Second content</p>
            <p>Second content</p>
            <p>Second content</p>
            <p>Second content</p>
          </div>
        </div>

        <div class="mdui-panel-item">
          <div class="mdui-panel-item-header">
            <div class="mdui-panel-item-title">邮件</div>
            <div class="mdui-panel-item-summary"></div>
            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
          </div>
          <div class="mdui-panel-item-body">
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
          </div>
        </div>

        <div class="mdui-panel-item">
          <div class="mdui-panel-item-header">
            <div class="mdui-panel-item-title">缓存</div>
            <div class="mdui-panel-item-summary">pdo</div>
            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
          </div>
          <div class="mdui-panel-item-body">
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
          </div>
        </div>

        <div class="mdui-panel-item">
          <div class="mdui-panel-item-header">
            <div class="mdui-panel-item-title">文件存储</div>
            <div class="mdui-panel-item-summary">又拍云</div>
            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
          </div>
          <div class="mdui-panel-item-body">
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
            <p>Third content</p>
          </div>
        </div>

      </div>
    </div>
  );
};
