import { h } from 'hyperapp';
import { Link, location } from '@hyperapp/router';
import cc from 'classcat';
import { JQ as $ } from 'mdui';
import timeHelper from '../../../../helper/time';
import './index.less';

export default ({ question }) => (global_state, global_actions) => (
  <Link
    to={$.path(`/questions/${question.question_id}`)}
    class={cc([
      'item',
      {
        'last-visit': global_state.question.question.question_id === question.question_id,
      },
    ])}
    key={question.question_id}
    oncreate={element => $(element).mutation()}
    onclick={() => {
      global_actions.questions.saveScrollPosition();
    }}
  >

    <div
      class="avatar"
      style={{
        backgroundImage: `url("${question.relationship.user.avatar.m}")`,
      }}
    ></div>

    <div class="content">
      <div class="title">{question.title}</div>
      <div class="meta">
        <div class="username">{question.relationship.user.username}</div>
        <div
          class="answer_time"
          title={timeHelper.format(question.answer_time || question.create_time)}
        >{(question.answer_time ? '回复于 ' : '发布于 ') + timeHelper.friendly(question.answer_time || question.create_time)}</div>
      </div>
    </div>

    <div class="more">
      <div class={cc([
        'answer_count',
        {
          'mdui-hidden': !question.answer_count,
        },
      ])}>{question.answer_count}</div>
    </div>

  </Link>
);
