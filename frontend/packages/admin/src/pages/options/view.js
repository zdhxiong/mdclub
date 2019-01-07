import { h } from 'hyperapp';
import './index.less';

import Loading from '../../components/loading';

const languageObject = {
  en: 'English',
  'zh-CN': '简体中文',
  'zh-TW': '繁体中文',
  ja: '日本語',
};

const smtpSecureObject = {
  '': '无',
  tls: 'TLS',
  ssl: 'SSL',
};

const cacheTypeObject = {
  pdo: 'PDO',
  redis: 'Redis',
  memcached: 'Memcached',
};

const storageTypeObject = {
  local: '本地文件系统',
  ftp: 'FTP 服务器',
  aliyun_oss: '阿里云 OSS',
  upyun: '又拍云',
  qiniu: '七牛云',
};

const ItemHeader = ({ label, value = false }) => (
  <div class="mdui-panel-item-header">
    <div class="mdui-panel-item-title">{label}</div>
    {value && <div class="mdui-panel-item-summary">{value}</div>}
    <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
  </div>
);

const Input = ({ label, name, value, helper = false, oninput }) => (
  <div class="mdui-textfield">
    <label class="mdui-textfield-label">{label}</label>
    <input class="mdui-textfield-input" name={name} type="text" value={value} oninput={oninput}/>
    {helper && <div class="mdui-textfield-helper">{helper}</div>}
  </div>
);

const Select = ({ label, name, value, data, onchange }) => (
  <div class="mdui-textfield">
    <label class="mdui-textfield-label">{label}</label>
    <select class="mdui-select" name={name} onchange={onchange} mdui-select>
      {Object.keys(data).map(v => <option value={v} selected={v === value}>{data[v]}</option>)}
    </select>
  </div>
);

const SaveBtn = ({ submitting, onsubmit }) => (
  <button
    class="mdui-btn mdui-color-theme"
    disabled={submitting}
    onclick={onsubmit}
  >{submitting ? '保存中…' : '保存'}</button>
);

export default (global_state, global_actions) => {
  const actions = global_actions.options;
  const state = global_state.options;

  return ({ match }) => (
    <div
      oncreate={element => actions.init({ element, global_actions })}
      ondestroy={element => actions.destroy({ element })}
      key={match.url}
      id="page-options"
    >
      <If condition={state.loading}><Loading/></If>
      <If condition={!state.loading}>
        <div class="mdui-panel" oncreate={element => actions.initPanel({ element })}>

          <div class="mdui-panel-item">
            <ItemHeader label="站点信息"/>
            <form class="mdui-panel-item-body" method="post">
              <Input
                label="网站名称"
                name="site_name"
                value={state.data.site_name}
                oninput={actions.data.input}
              />
              <Input
                label="网站关键词"
                name="site_keywords"
                helper="多个关键词之间用半角逗号隔开"
                value={state.data.site_keywords}
                oninput={actions.data.input}
              />
              <Input
                label="网站简介"
                name="site_description"
                value={state.data.site_description}
                oninput={actions.data.input}
              />
              <Input
                label="ICP 备案号"
                name="site_icp_beian"
                value={state.data.site_icp_beian}
                oninput={actions.data.input}
              />
              <Input
                label="公安备案号"
                name="site_gongan_beian"
                value={state.data.site_gongan_beian}
                oninput={actions.data.input}
              />
              <Select
                label="语言"
                name="language"
                value={state.data.language}
                data={languageObject}
                onchange={actions.data.input}
              />
              <Input
                label="静态资源访问路径"
                name="site_static_url"
                value={state.data.site_static_url}
                oninput={actions.data.input}
              />
              <div class="mdui-panel-item-actions">
                <SaveBtn submitting={state.submitting} onsubmit={actions.submit}/>
              </div>
            </form>
          </div>

          <div class="mdui-panel-item">
            <ItemHeader label="主题" value={state.data.theme}/>
            <div class="mdui-panel-item-body">
              <p>Second content</p>
            </div>
          </div>

          <div class="mdui-panel-item">
            <ItemHeader label="邮箱"/>
            <form class="mdui-panel-item-body" method="post">
              <Input
                label="SMTP 服务器"
                name="smtp_host"
                value={state.data.smtp_host}
                oninput={actions.data.input}
              />
              <Input
                label="SMTP 账户"
                name="smtp_username"
                value={state.data.smtp_username}
                oninput={actions.data.input}
              />
              <Input
                label="SMTP 密码"
                name="smtp_password"
                value={state.data.smtp_password}
                oninput={actions.data.input}
              />
              <Input
                label="SMTP 端口"
                name="smtp_port"
                value={state.data.smtp_port}
                oninput={actions.data.input}
              />
              <Select
                label="加密连接类型"
                name="smtp_secure"
                value={state.data.smtp_secure}
                data={smtpSecureObject}
                onchange={actions.data.input}
              />
              <Input
                label="接收回复邮件的邮箱地址"
                name="smtp_reply_to"
                value={state.data.smtp_reply_to}
                oninput={actions.data.input}
              />
              <div class="mdui-panel-item-actions">
                <SaveBtn submitting={state.submitting} onsubmit={actions.submit}/>
                <button class="mdui-btn mdui-ripple" type="button" onclick={actions.sendTestEmail}>发送测试邮件</button>
              </div>
            </form>
          </div>

          <div class="mdui-panel-item">
            <ItemHeader label="缓存" value={cacheTypeObject[state.data.cache_type]}/>
            <div class="mdui-panel-item-body">
              <Select
                label="缓存类型"
                name="cache_type"
                value={state.data.cache_type}
                data={cacheTypeObject}
                onchange={actions.data.input}
              />
              <If condition={state.data.cache_type === 'redis'}>
                <Input
                  label="Redis 服务器地址"
                  name="cache_redis_host"
                  value={state.data.cache_redis_host}
                  oninput={actions.data.input}
                />
                <Input
                  label="Redis 用户名"
                  name="cache_redis_username"
                  value={state.data.cache_redis_username}
                  oninput={actions.data.input}
                />
                <Input
                  label="Redis 密码"
                  name="cache_redis_password"
                  value={state.data.cache_redis_password}
                  oninput={actions.data.input}
                />
                <Input
                  label="Redis 端口号"
                  name="cache_redis_port"
                  value={state.data.cache_redis_port}
                  oninput={actions.data.input}
                />
              </If>
              <If condition={state.data.cache_type === 'memcached'}>
                <Input
                  label="Memcached 服务器地址"
                  name="cache_memcached_host"
                  value={state.data.cache_memcached_host}
                  oninput={actions.data.input}
                />
                <Input
                  label="Memcached 用户名"
                  name="cache_memcached_username"
                  value={state.data.cache_memcached_username}
                  oninput={actions.data.input}
                />
                <Input
                  label="Memcached 密码"
                  name="cache_memcached_password"
                  value={state.data.cache_memcached_password}
                  oninput={actions.data.input}
                />
                <Input
                  label="Memcached 端口号"
                  name="cache_memcached_port"
                  value={state.data.cache_memcached_port}
                  oninput={actions.data.input}
                />
              </If>
              <div class="mdui-panel-item-actions">
                <SaveBtn submitting={state.submitting} onsubmit={actions.submit}/>
              </div>
            </div>
          </div>

          <div class="mdui-panel-item">
            <ItemHeader label="文件存储" value={storageTypeObject[state.data.storage_type]}/>
            <div class="mdui-panel-item-body">
              <Select
                label="存储类型"
                name="storage_type"
                value={state.data.storage_type}
                data={storageTypeObject}
                onchange={actions.data.input}
              />
              <If condition={state.data.storage_type === 'local'}>

              </If>
              <If condition={state.data.storage_type === 'ftp'}>

              </If>
              <If condition={state.data.storage_type === 'aliyun_oss'}>

              </If>
              <If condition={state.data.storage_type === 'upyun'}>

              </If>
              <If condition={state.data.storage_type === 'qiniu'}>

              </If>
              <div class="mdui-panel-item-actions">
                <SaveBtn submitting={state.submitting} onsubmit={actions.submit}/>
              </div>
            </div>
          </div>

        </div>
      </If>
    </div>
  );
};
