import { h } from 'hyperapp';
import { location } from '@hyperapp/router';
import { JQ as $ } from 'mdui';
import './index.less';

import rawHtml from '../../../../helper/rawHtml';

import UserLine from '../../../../components/user-line';

export default ({ answer }) => (
  <div
    class="item"
    key={answer.answer_id}
    oncreate={element => $(element).mutation()}
  >
    <UserLine user={answer.relationship.user} time={answer.create_time}/>
    <div
      class="content mdui-typo"
      oncreate={rawHtml(answer.content_rendered)}
      onupdate={rawHtml(answer.content_rendered)}
    ></div>
  </div>
);
