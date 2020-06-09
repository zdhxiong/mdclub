import { h } from 'hyperapp';
import {
  isPathArticles,
  isPathQuestions,
  isPathTopics,
  isPathUsers,
} from '~/utils/path';

import Tab from '~/components/tab/view.jsx';

export default ({ user }) => (
  <Choose>
    <When condition={isPathQuestions()}>
      <Tab
        key="questions"
        centered={true}
        items={[
          { name: '最新', hash: 'recent' },
          { name: '近期热门', hash: 'popular' },
        ].concat(user ? { name: '已关注', hash: 'following' } : null)}
      />
    </When>

    <When condition={isPathArticles()}>
      <Tab
        key="articles"
        centered={true}
        items={[
          { name: '最新', hash: 'recent' },
          { name: '近期热门', hash: 'popular' },
        ].concat(user ? { name: '已关注', hash: 'following' } : null)}
      />
    </When>

    <When condition={user && isPathTopics()}>
      <Tab
        key="topics"
        centered={true}
        items={[
          { name: '已关注', hash: 'following' },
          { name: '精选', hash: 'recommended' },
        ]}
      />
    </When>

    <When condition={user && isPathUsers()}>
      <Tab
        key="users"
        centered={true}
        items={[
          { name: '已关注', hash: 'followees' },
          { name: '关注者', hash: 'followers' },
          { name: '找人', hash: 'recommended' },
        ]}
      />
    </When>
  </Choose>
);
