import { h } from 'hyperapp';
import Submit from '~/common/account/components/submit.jsx';

export default ({ state }) => (
  <div class="actions mdui-clearfix">
    <Submit
      disabled={state.submitting}
      text={state.submitting ? '注册中…' : '注册'}
    />
  </div>
);
