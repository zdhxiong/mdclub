import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import UserPopover from '~/components/user-popover/view.jsx';
import Header from '../header/view.jsx';
import { summaryText } from '~/utils/html';

export default ({
  title,
  items,
  primaryKey,
  dataName,
  loading,
  url,
  actions,
}) => (
  <div class="items-container">
    <div class="items mdui-card">
      <Header title={title} url={url} />
      <div class="content">
        <Loading show={loading} />
        {items.map((item) => (
          <Link
            class="item"
            to={fullPath(`${url.split('#')[0]}/${item[primaryKey]}`)}
            onclick={() => actions.afterItemClick(item)}
          >
            <UserPopover
              actions={actions}
              user={item.relationships.user}
              dataName={dataName}
              primaryKey={primaryKey}
              primaryValue={item[primaryKey]}
            >
              <div
                class="avatar user-popover-trigger"
                style={{
                  backgroundImage: `url("${item.relationships.user.avatar.middle}")`,
                }}
              />
            </UserPopover>
            <div
              class="title mdui-text-color-theme-text"
              oncreate={summaryText(item.title)}
              onupdate={summaryText(item.title)}
            />
          </Link>
        ))}
      </div>
    </div>
  </div>
);
