import { h } from 'hyperapp';
import $ from 'mdui.jq';

const valueFormat = (value) => {
  if (value < 60) {
    return value;
  }

  if (value < 3600) {
    return value / 60;
  }

  if (value < 86400) {
    return value / 3600;
  }

  return value / 86400;
};

const onInput = (e) => {
  const $currentInput = $(e.target);
  const $timeIn = $currentInput.parents('.time-in');
  const $inputNumber = $timeIn.find('.time-in-number');
  const $inputUnit = $timeIn.find('.time-in-unit');
  const $inputValue = $timeIn.find('.time-in-value');

  const value =
    parseInt($inputNumber.val(), 10) * parseInt($inputUnit.val(), 10);

  $inputValue.val(value);
};

export default ({ name, value }) => (
  <div class="time-in">
    仅发布后
    <div class="mdui-textfield">
      <input
        class="mdui-textfield-input time-in-number"
        type="number"
        value={valueFormat(value)}
        oninput={onInput}
      />
    </div>
    <select
      class="mdui-select time-in-unit"
      mdui-select
      oncreate={(element) => $(element).mutation()}
      onchange={onInput}
    >
      <option value="1" selected={value > 0 && value < 60}>
        秒
      </option>
      <option
        value="60"
        selected={value === 0 || (value >= 60 && value < 3600)}
      >
        分钟
      </option>
      <option value="3600" selected={value >= 3600 && value < 86400}>
        小时
      </option>
      <option value="86400" selected={value >= 86400}>
        天
      </option>
    </select>
    <input type="hidden" class="time-in-value" name={name} value={value} />内
  </div>
);
