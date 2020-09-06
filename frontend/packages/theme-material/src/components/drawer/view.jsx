import { h } from 'hyperapp';
import cc from 'classcat';
import { Link } from 'hyperapp-router';
import {
  fullPath,
  isPathArticle,
  isPathArticles,
  isPathInbox,
  isPathIndex,
  isPathNotifications,
  isPathQuestion,
  isPathQuestions,
  isPathTopic,
  isPathTopics,
  isPathUser,
  isPathUsers,
} from '~/utils/path';
import './index.less';

const { site_name, site_icp_beian, site_gongan_beian } = window.G_OPTIONS;
const site_gongan_beian_code = site_gongan_beian
  ? site_gongan_beian.match(/\d+/)[0]
  : '';

const Item = ({ url, icon, title, active }) => (
  <Link
    to={fullPath(url)}
    class={cc([
      'mdui-list-item',
      'mdui-ripple',
      {
        'mdui-list-item-active': active,
      },
    ])}
  >
    <i class="mdui-list-item-icon mdui-icon material-icons">{icon}</i>
    <div class="mdui-list-item-content">{title}</div>
  </Link>
);

export default ({ user, interviewee }) => (
  <div class="mc-drawer mdui-drawer">
    <div class="mdui-list">
      <Item url="/" icon="home" title="首页" active={isPathIndex()} />
      <Item
        url="/topics"
        icon="class"
        title="话题"
        active={isPathTopics() || isPathTopic()}
      />
      <Item
        url="/questions"
        icon="forum"
        title="问答"
        active={isPathQuestions() || isPathQuestion()}
      />
      <Item
        url="/articles"
        icon="description"
        title="文章"
        active={isPathArticles() || isPathArticle()}
      />
      <div class="mdui-divider" />
      <If condition={user}>
        <Item
          url={`/users/${user.user_id}`}
          icon="account_circle"
          title="个人资料"
          active={
            isPathUser() && interviewee && user.user_id === interviewee.user_id
          }
        />
      </If>
      <Item
        url="/users"
        icon="people"
        title="人脉"
        active={
          isPathUsers() ||
          (isPathUser() &&
            (!user || (interviewee && user.user_id !== interviewee.user_id)))
        }
      />
      <If condition={user}>
        <Item
          url="/notifications"
          icon="notifications"
          title="通知"
          active={isPathNotifications()}
        />
        {/* <Item url="/inbox" icon="mail" title="私信" active={isPathInbox()} /> */}
      </If>
    </div>
    <div class="copyright">
      <If condition={site_icp_beian}>
        <p>
          <a href="https://beian.miit.gov.cn/" target="_blank">
            {site_icp_beian}
          </a>
        </p>
      </If>
      <If condition={site_gongan_beian}>
        <p>
          <a
            href={`http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=${site_gongan_beian_code}`}
            target="_blank"
          >
            {site_gongan_beian}
          </a>
        </p>
      </If>
      <p>
        © {new Date().getFullYear()} {site_name}
      </p>
      <p>
        Powered by{' '}
        <a href="https://mdui.org" target="_blank">
          MDUI
        </a>{' '}
        &{' '}
        <a href="https://mdclub.org" target="_blank">
          MDClub
        </a>
      </p>
    </div>
  </div>
);
