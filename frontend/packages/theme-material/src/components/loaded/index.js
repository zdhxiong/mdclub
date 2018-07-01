import { h } from 'hyperapp';
import cc from 'classcat';
import './index.less';

export default ({ show }) => (
  <div
    class={cc([
      'mc-loaded',
      'mdui-text-center',
      'mdui-m-y-3',
      'mdui-typo-caption-opacity',
      {
        'mdui-hidden': !show,
      },
    ])}
  >已加载完所有数据</div>
);
