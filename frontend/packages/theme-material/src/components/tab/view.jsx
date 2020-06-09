import { h } from 'hyperapp';
import cc from 'classcat';
import { isUndefined } from 'mdui.jq/es/utils';
import './index.less';

export default ({ items, centered = false, key = null }) => (
  <div
    key={key}
    class={cc([
      'mc-tab',
      'mdui-tab',
      {
        'mdui-tab-centered': centered,
      },
    ])}
  >
    {items.map((item) => {
      if (!item) {
        return null;
      }

      return (
        <a href={`#${item.hash}`} class="mdui-ripple">
          {item.name}
          <If condition={!isUndefined(item.count)}>
            <span>{item.count}</span>
          </If>
        </a>
      );
    })}
  </div>
);
