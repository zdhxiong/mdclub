import { h } from 'hyperapp';
import { JQ as $ } from 'mdui';
import './index.less';

export default () => (
  <div
    oncreate={element => $(element).mutation()}
    class="mc-loading mdui-spinner mdui-center mdui-m-y-5"
  ></div>
);
