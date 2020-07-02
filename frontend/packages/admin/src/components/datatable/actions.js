import mdui from 'mdui';
import $ from 'mdui.jq';
import extend from 'mdui.jq/es/functions/extend';
import { $document } from 'mdui/es/utils/dom';
import commonActions from '~/utils/actionsAbstract';
import { emit, subscribe } from '~/utils/pubsub';
import { apiCatch } from '~/utils/errorHandlers';
import { findItem } from '~/utils/func';
import stateDefault from './stateDefault';

const as = {
  /**
   * 初始化
   */
  onCreate: ({ element }) => (state, actions) => {
    // 表格滚动时，表头阴影变化
    $(element)
      .find('tbody')
      .on('scroll', (e) => {
        actions.setState({ isScrollTop: !e.target.scrollTop });
      });

    // 更新完表格结构后，绑定右键菜单
    subscribe('datatable_state_update', ({ buttons }) => {
      // 确保菜单初始化只执行一次
      let $wrapper = $('.datatable-contextmenu-wrapper');
      if ($wrapper.length) {
        return;
      }

      // 创建用于定位菜单的隐藏元素
      $wrapper = $('<div class="datatable-contextmenu-wrapper">')
        .css({
          position: 'absolute',
        })
        .appendTo('body');

      // 创建菜单
      const $contextmenu = $(
        '<ul class="mdui-menu datatable-contextmenu">',
      ).appendTo('body');

      buttons.forEach((button) => {
        const isTarget = button.type === 'target';
        const icon = isTarget ? 'open_in_new' : button.icon;
        const label = isTarget ? '新窗口打开' : button.label;
        const target = isTarget ? '_blank' : '_self';

        const $menuItem = `
          <li class="mdui-menu-item">
            <a href="javascript:;" target="${target}" class="mdui-ripple">
              <i class="mdui-menu-item-icon mdui-icon material-icons">${icon}</i>${label}
            </a>
          </li>
        `;
        $contextmenu.append($menuItem);
      });

      const menu = new mdui.Menu($wrapper, $contextmenu);

      // 绑定右键事件
      $document.on('contextmenu', (e) => {
        const $target = $(e.target);
        const isTr = $target.is('.mc-datatable tbody tr');
        const isTrChild = $target.parents('tr').is('.mc-datatable tbody tr');

        // 仅在表格行上右键点击时弹出菜单
        if (!isTr && !isTrChild) {
          return;
        }

        // 0：移动端长按（iOS 测试未通过）
        // 2：电脑端右键
        if (e.button !== 2 && e.button !== 0) {
          return;
        }

        e.preventDefault();

        // 获取当前点击行的数据
        const $tr = isTr ? $target : $target.parents('tr');
        const primaryValue = $tr.data('id');
        const currentState = actions.getState();
        const row = findItem(
          currentState.data,
          currentState.primaryKey,
          primaryValue,
        );

        // 给菜单项绑定事件
        buttons.forEach((button, index) => {
          switch (button.type) {
            case 'target':
              menu.$element
                .find('a')
                .eq(index)
                .attr('href', button.getTargetLink(row));
              break;

            case 'btn':
              menu.$element.find('a').get(index).onclick = (ae) => {
                ae.preventDefault();
                button.onClick(row);
              };
              break;

            default:
              break;
          }
        });

        // 仅选中当前点击行
        actions.uncheckAll();
        actions.checkOne(primaryValue);

        // 鼠标点击位置，相对于页面
        const position = {
          top: `${e.pageY}px`,
          left: `${e.pageX}px`,
        };

        $wrapper.css(position);
        menu.$element.css(position);
        menu.open();
      });
    });
  },

  /**
   * 销毁前执行
   */
  onDestroy: ({ element }) => (state, actions) => {
    $(element).find('tbody').off('scroll');
    $('.datatable-contextmenu-wrapper').remove();
    $('.datatable-contextmenu').remove();

    actions.setState(stateDefault);
  },

  /**
   * 开始加载数据
   */
  loadStart: () => (state, actions) => {
    actions.setState({ data: [], loading: true });
  },

  /**
   * 数据加载失败
   */
  loadFail: (response) => (state, actions) => {
    actions.setState({ loading: false });
    apiCatch(response);
  },

  /**
   * 数据加载成功
   */
  loadSuccess: ({ data, pagination }) => (state, actions) => {
    const isCheckedRows = {};
    data.map((item) => {
      isCheckedRows[item[state.primaryKey]] = false;
    });

    actions.setState({
      loading: false,
      isCheckedRows,
      isCheckedAll: false,
      checkedCount: 0,
      data,
    });

    emit('pagination_state_update', pagination);
  },

  /**
   * 更新某一行的数据
   */
  updateOne: (row) => (state, actions) => {
    const { data, primaryKey } = state;

    data.forEach((item, index) => {
      if (item[primaryKey] === row[primaryKey]) {
        data[index] = row;
      }
    });

    actions.setState({ data });
  },

  /**
   * 切换某一行的选中状态
   */
  checkOne: (rowId) => (state, actions) => {
    const { isCheckedRows } = state;
    isCheckedRows[rowId] = !isCheckedRows[rowId];

    let checkedCount = 0;
    let isCheckedAll = true;

    Object.keys(isCheckedRows).map((rowIdTmp) => {
      if (!isCheckedRows[rowIdTmp]) {
        isCheckedAll = false;
      } else {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },

  /**
   * 切换全部行的选中状态
   */
  checkAll: (e) => (state, actions) => {
    let checkedCount = 0;
    const isCheckedAll = e.target.checked;
    const { isCheckedRows } = state;

    Object.keys(isCheckedRows).map((rowIdTmp) => {
      isCheckedRows[rowIdTmp] = isCheckedAll;

      if (isCheckedAll) {
        checkedCount += 1;
      }
    });

    actions.setState({ isCheckedRows, isCheckedAll, checkedCount });
  },

  /**
   * 全部行取消选中
   */
  uncheckAll: () => (state, actions) => {
    const checkedCount = 0;
    const { isCheckedRows } = state;

    Object.keys(isCheckedRows).map((rowIdTmp) => {
      isCheckedRows[rowIdTmp] = false;
    });

    actions.setState({ isCheckedRows, isCheckedAll: false, checkedCount });
  },

  /**
   * 修改排序方式
   */
  changeOrder: ({ e, order, onChange }) => (state, actions) => {
    e.preventDefault();

    actions.setState({ order });

    onChange();
  },
};

export default extend(as, commonActions);
