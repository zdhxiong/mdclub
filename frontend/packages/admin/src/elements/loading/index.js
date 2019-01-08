import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import './index.less';

export default () => (
  <div
    class="me-loading mdui-spinner"
    key="me-loading"
    oncreate={element => $(element).mutation()}
  ></div>
);
