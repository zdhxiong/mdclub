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
    >
      <div class="mdui-panel">

        <div class="mdui-panel-item">
          <div class="mdui-panel-item-header">
            <div class="mdui-panel-item-title">站点信息</div>
            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
          </div>
          <div class="mdui-panel-item-body">
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">网站名称</label>
              <input class="mdui-textfield-input" name="site_name" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">网站关键词</label>
              <input class="mdui-textfield-input" name="site_keywords" type="text"/>
              <div class="mdui-textfield-helper">多个关键词之间用半角逗号隔开</div>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">网站简介</label>
              <input class="mdui-textfield-input" name="site_description" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">ICP 备案号</label>
              <input class="mdui-textfield-input" name="site_icp_beian" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">公安备案号</label>
              <input class="mdui-textfield-input" name="site_gongan_beian" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">语言</label>
              <input class="mdui-textfield-input" name="language" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">静态资源访问路径</label>
              <input class="mdui-textfield-input" name="site_static_url" type="text"/>
            </div>
            <div class="mdui-panel-item-actions">
              <button class="mdui-btn mdui-ripple">保存</button>
            </div>
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
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">SMTP 服务器</label>
              <input class="mdui-textfield-input" name="smtp_host" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">SMTP 账户</label>
              <input class="mdui-textfield-input" name="smtp_username" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">SMTP 密码</label>
              <input class="mdui-textfield-input" name="smtp_password" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">SMTP 端口</label>
              <input class="mdui-textfield-input" name="smtp_port" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">加密连接类型</label>
              <select class="mdui-select" mdui-select>
                <option value="">无</option>
                <option value="tls">tls</option>
                <option value="ssl">ssl</option>
              </select>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">加密连接类型</label>
              <input class="mdui-textfield-input" name="smtp_secure" type="text"/>
            </div>
            <div class="mdui-textfield">
              <label class="mdui-textfield-label">接收回复邮件的邮箱地址</label>
              <input class="mdui-textfield-input" name="smtp_reply_to" type="text"/>
            </div>
            <div class="mdui-panel-item-actions">
              <button class="mdui-btn mdui-ripple">保存</button>
            </div>
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
