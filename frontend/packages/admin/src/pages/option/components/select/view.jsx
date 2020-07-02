import { h } from 'hyperapp';
import $ from 'mdui.jq';

export default ({ label, name, value, data, onChange = null }) => (
  <div class="mdui-textfield" oncreate={(element) => $(element).mutation()}>
    <label class="mdui-textfield-label">{label}</label>
    <select class="mdui-select" mdui-select name={name} onchange={onChange}>
      {Object.keys(data).map((v) => (
        <option value={v} selected={v === value}>
          {data[v]}
        </option>
      ))}
    </select>
  </div>
);
