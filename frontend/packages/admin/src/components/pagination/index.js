import { h } from 'hyperapp';
import { Link } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
import './index.less';

export default ({
  onPrevPageClick, // 上一页按钮点击事件
  onNextPageClick, // 下一页按钮点击事件
  onPerPageChange, // 每页行数切换事件
  onPageChange, // 页码修改事件
  pagination, // 分页参数
                }) => {
  if (!pagination) {
    return {};
  }

  return () => (
    <div class="mc-pagination mdui-toolbar" oncreate={element => $(element).mutation() }>
      <div class="mdui-toolbar-spacer"></div>
      <span class="per-page-label mdui-typo-caption">每页行数：</span>
      <select mdui-select onchange={onPerPageChange}>
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <div class="page mdui-typo-caption">
        第
        <form class="now-page-form" onsubmit={onPageChange}>
          <input
            class="now-page-input mdui-textfield-input"
            type="text"
            value={pagination.page}
          />
        </form>
        页，共 {pagination.pages} 页
      </div>
      <button
        class="page-prev mdui-btn mdui-btn-icon mdui-ripple"
        mdui-tooltip="{content: '上一页'}"
        disabled={!pagination.previous}
        onclick={onPrevPageClick}
      >
        <i class="mdui-icon material-icons">chevron_left</i>
      </button>
      <button
        class="page-next mdui-btn mdui-btn-icon mdui-ripple"
        mdui-tooltip="{content: '下一页'}"
        disabled={!pagination.next}
        onclick={onNextPageClick}
      >
        <i class="mdui-icon material-icons">chevron_right</i>
      </button>
    </div>
  );
};
