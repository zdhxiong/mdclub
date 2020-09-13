import { h } from 'hyperapp';
import $ from 'mdui.jq';
import './index.less';

import Loading from '~/components/loading/view.jsx';

const CountItem = ({ label, count }) => (
  <div class="number-item">
    <div class="label mdui-text-color-theme-secondary">{label}</div>
    <div class="number mdui-text-color-theme-text">{count}</div>
  </div>
);

const SystemInfoItem = ({ label, value, notice = null }) => (
  <div class="item">
    <label>
      {label}
      <If condition={notice}>
        <i
          class="mdui-icon material-icons"
          mdui-tooltip={`{content: '${notice}', delay: 300}`}
        >
          help_outline
        </i>
      </If>
    </label>
    <span class="mdui-text-color-theme-secondary">{value}</span>
  </div>
);

export default (state, actions) => ({ match }) => {
  const { system_info } = state;

  const NewDataStats = ({ title, type, newData }) => (
    <div class="new-data mdui-card mdui-card-shadow">
      <div class="header">
        <div class="title mdui-text-color-theme-text">{title}</div>
        <select
          class="mdui-select"
          mdui-select
          disabled={newData.loading}
          oncreate={(element) => $(element).mutation()}
          onchange={(e) => actions.onDateChange({ e, type })}
        >
          <option value="7day" selected={newData.range === '7day'}>
            最近 7 天
          </option>
          <option value="this-month" selected={newData.range === 'this-month'}>
            本月
          </option>
          <option value="last-month" selected={newData.range === 'last-month'}>
            上月
          </option>
          <option value="30day" selected={newData.range === '30day'}>
            最近 30 天
          </option>
          <option value="this-year" selected={newData.range === 'this-year'}>
            今年
          </option>
          <option value="last-year" selected={newData.range === 'last-year'}>
            去年
          </option>
          <option value="365day" selected={newData.range === '365day'}>
            最近 1 年
          </option>
        </select>
      </div>
      <Loading show={newData.loading} />
      <If condition={!newData.loading}>
        <div
          class="chart-wrapper"
          oncreate={(element) =>
            actions.onChartCreate({ element, data: newData.data })
          }
        />
      </If>
    </div>
  );

  return (
    <div
      oncreate={actions.onCreate}
      ondestroy={actions.onDestroy}
      key={match.url}
      class="mdui-container"
      id="page-index"
    >
      <Loading show={state.loading} />

      <If condition={system_info}>
        <div class="total-number mdui-card mdui-card-shadow">
          <div class="header">
            <div class="title mdui-text-color-theme-text">数据总量</div>
          </div>
          <div class="rows">
            <CountItem label="用户量" count={state.total_user} />
            <CountItem label="文章量" count={state.total_article} />
            <CountItem label="提问量" count={state.total_question} />
            <CountItem label="回答量" count={state.total_answer} />
            <CountItem label="评论量" count={state.total_comment} />
          </div>
        </div>

        <div class="system-info mdui-card mdui-card-shadow">
          <div class="header">
            <div class="title mdui-text-color-theme-text">系统信息</div>
          </div>
          <div class="items">
            <SystemInfoItem
              label="MDClub 版本"
              value={system_info.mdclub_version}
            />
            <SystemInfoItem label="操作系统" value={system_info.os_version} />
            <SystemInfoItem label="PHP 版本" value={system_info.php_version} />
            <SystemInfoItem
              label="Web 服务器软件"
              value={system_info.webserver_version}
            />
            <SystemInfoItem
              label="数据库版本"
              value={system_info.database_version}
            />
            <SystemInfoItem
              label="上传文件限制"
              value={system_info.upload_max_filesize}
              notice="可通过修改 php.ini 文件中的 upload_max_filesize 来修改该项。若遇到“图片上传失败”，请检查 post_max_size 必须大于 upload_max_filesize 的值"
            />
            <SystemInfoItem
              label="PHP 执行时长限制"
              value={system_info.max_execution_time}
              notice="可通过修改 php.ini 文件中的 max_execution_time 来修改该项"
            />
            <SystemInfoItem
              label="剩余硬盘空间"
              value={system_info.disk_free_space}
            />
            <SystemInfoItem
              label="数据库大小"
              value={system_info.database_size}
            />
          </div>
        </div>

        <NewDataStats
          title="用户增长"
          type="new_user"
          newData={state.new_user}
        />
        <NewDataStats
          title="文章数增长"
          type="new_article"
          newData={state.new_article}
        />
        <NewDataStats
          title="提问数增长"
          type="new_question"
          newData={state.new_question}
        />
        <NewDataStats
          title="回答数增长"
          type="new_answer"
          newData={state.new_answer}
        />
        <NewDataStats
          title="评论数增长"
          type="new_comment"
          newData={state.new_comment}
        />
      </If>
    </div>
  );
};
