import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import commonActions from '~/utils/actionsAbstract';

let menu;

const as = {
  onCreate: ({ element }) => {
    const $element = $(element);
    const $bar = $element.find('.bar');
    const $form = $element.find('.form');

    menu = new mdui.Menu($bar, $form, {
      position: 'bottom',
      align: 'left',
      covered: false,
    });

    $form
      .on('open.mdui.menu', () => {
        $form.innerWidth($bar.width());
        $element.addClass('is-open');
      })
      .on('opened.mdui.menu', () => {
        // 打开后，聚焦到第一个输入框
        const $firstInput = $form.find('input');

        if ($firstInput.length) {
          $firstInput.get(0).focus();
        }
      })
      .on('close.mdui.menu', () => {
        $element.removeClass('is-open');
      });
  },

  /**
   * 点击纸片事件
   */
  onChipClick: () => {
    setTimeout(() => menu.close());
  },

  /**
   * 点击纸片的删除按钮事件
   */
  onChipDelete: ({ name }) => (state, actions) => {
    const { data } = state;

    data[name] = '';

    actions.setState(data);
    $document.trigger('search-submit');
  },

  /**
   * 提交搜索
   */
  onSubmit: (e) => (state, actions) => {
    e.preventDefault();

    const formArr = $(e.target).serializeArray();
    const data = {};
    let isDataEmpty = true;

    formArr.map((field) => {
      if (field.value === '') {
        return;
      }

      data[field.name] = field.value;
      isDataEmpty = false;
    });

    actions.setState({ data, isDataEmpty });

    menu.close();

    $(document).trigger('search-submit');
  },
};

export default extend(as, commonActions);
