import { h } from 'hyperapp';

export default ({ label, name, value, onChange = null }) => (
  <label class="mdui-switch">
    {label}
    <input
      type="checkbox"
      name={name}
      checked={value}
      value={value}
      onchange={onChange}
    />
    <i class="mdui-switch-icon" />
  </label>
);
