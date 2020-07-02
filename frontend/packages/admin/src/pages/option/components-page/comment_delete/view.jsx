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
        label="是否允许用户删除评论"
        name="comment_can_delete"
        value={data.comment_can_delete}
        onChange={(e) => {
          data.comment_can_delete = e.target.checked;
          actions.setState({ data });
        }}
      />
      <div class={cc([{ 'mdui-hidden': !data.comment_can_delete }])}>
        <div class="subheader mdui-text-color-theme-secondary">
          何时允许删除评论
        </div>
        <TimeIn
          name="comment_can_delete_before"
          value={data.comment_can_delete_before}
        />
      </div>
      <SaveBtn submitting={submitting} />
    </form>
  );
};
