import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import cc from 'classcat';
import './index.less';

export default () => (
  <div
    oncreate={element => $(element).mutation()}
    class={cc([
      'mc-loading',
      'mdui-spinner',
      'mdui-center',
      'mdui-m-y-3',
    ])}
  ></div>
);
