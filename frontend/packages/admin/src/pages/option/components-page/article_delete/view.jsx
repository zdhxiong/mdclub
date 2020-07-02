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
        label="是否允许用户删除文章"
        name="article_can_delete"
        value={data.article_can_delete}
        onChange={(e) => {
          data.article_can_delete = e.target.checked;
          actions.setState({ data });
        }}
      />
      <div class={cc([{ 'mdui-hidden': !data.article_can_delete }])}>
        <div class="subheader mdui-text-color-theme-secondary">
          何时允许删除文章
        </div>
        <TimeIn
          name="article_can_delete_before"
          value={data.article_can_delete_before}
        />
        <div class="mdui-divider" />
        <Switch
          label="仅没有评论时"
          name="article_can_delete_only_no_comment"
          value={data.article_can_delete_only_no_comment}
        />
      </div>
      <SaveBtn submitting={submitting} />
    </form>
  );
};
