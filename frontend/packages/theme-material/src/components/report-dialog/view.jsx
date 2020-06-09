import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

export default ({ state, actions }) => {
  const Item = ({ reason }) => (
    <label class="mdui-list-item">
      <div class="mdui-radio">
        <input
          type="radio"
          name="reason"
          value={reason}
          checked={reason === state.reason}
          onchange={(event) => actions.onChange(event)}
        />
        <i class="mdui-radio-icon" />
      </div>
      <div class="mdui-list-item-content">{reason}</div>
    </label>
  );

  return (
    <div
      oncreate={(element) => actions.onCreate({ element })}
      key="report-dialog"
      class="mc-report-dialog mdui-dialog"
    >
      <div class="mdui-dialog-title">
        <span>举报</span>
        <button
          class="mdui-btn mdui-btn-icon mdui-ripple"
          onclick={actions.close}
        >
          <i class="mdui-icon material-icons mdui-text-color-theme-icon">
            close
          </i>
        </button>
      </div>
      <form class="mdui-dialog-content" method="post">
        <div class="mdui-list">
          <Item reason="垃圾广告信息" />
          <Item reason="不友善行为" />
          <Item reason="有害信息" />
          <Item reason="涉嫌侵权" />
          <Item reason="诱导赞同、关注等行为" />
          <Item reason="其他原因" />
          <div
            class={cc([
              'mdui-textfield',
              {
                'mdui-hidden': state.reason !== '其他原因',
              },
            ])}
          >
            <textarea
              class="custom-reason mdui-textfield-input"
              placeholder="请输入原因"
            />
          </div>
        </div>
      </form>
      <div class="mdui-dialog-actions">
        <button class="mdui-btn mdui-ripple" mdui-dialog-cancel>
          取消
        </button>
        <button class="mdui-btn mdui-ripple" mdui-dialog-confirm>
          举报
        </button>
      </div>
    </div>
  );
};
