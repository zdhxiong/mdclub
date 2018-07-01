import { h } from 'hyperapp';
import $ from 'mdui.JQ';
import cc from 'classcat';
import './index.less';

export default ({ show }) => (
  <div
    oncreate={element => $(element).mutation()}
    class={cc([
      'mc-loading',
      'mdui-spinner',
      'mdui-center',
      'mdui-m-y-3',
      {
        'mdui-hidden': !show,
      },
    ])}
  ></div>
);
