import { h } from 'hyperapp';
import cc from 'classcat';

import Switch from '../../components/switch/view.jsx';
import TimeIn from '../../components/time-in/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Switch
        label="是否允许用户编辑提问"
        name="question_can_edit"
        value={data.question_can_edit}
        onChange={(e) => {
          data.question_can_edit = e.target.checked;
          actions.setState({ data });
        }}
      />
      <div class={cc([{ 'mdui-hidden': !data.question_can_edit }])}>
        <div class="subheader mdui-text-color-theme-secondary">
          何时允许编辑提问
        </div>
        <TimeIn
          name="question_can_edit_before"
          value={data.question_can_edit_before}
        />
        <div class="mdui-divider" />
        <Switch
          label="仅没有回答时"
          name="question_can_edit_only_no_answer"
          value={data.question_can_edit_only_no_answer}
        />
        <div class="mdui-divider" />
        <Switch
          label="仅没有评论时"
          name="question_can_edit_only_no_comment"
          value={data.question_can_edit_only_no_comment}
        />
      </div>
      <SaveBtn submitting={submitting} />
    </form>
  );
};
