import { JQ as $ } from 'mdui';
import Bold from './bold';
import Italic from './italic';
import Head from './head';
import Code from './code';
import { Ol, Ul } from './list';
import Link from './link';
import Image from './image';
import ClearDrafts from './clear_drafts';

const MenuConstructors = {
  bold: Bold,
  italic: Italic,
  head: Head,
  code: Code,
  ol: Ol,
  ul: Ul,
  link: Link,
  image: Image,
  clear_drafts: ClearDrafts,
};

/**
 * 构造函数
 * @param editor
 * @constructor
 */
function Menus(editor) {
  this.editor = editor;
  this.menus = {};
  this._init();
}

Menus.prototype = {
  constructor: Menus,

  /**
   * 初始化菜单
   * @private
   */
  _init() {
    const editor = this.editor;

    editor.options.menus.forEach((name) => {
      // 插入分隔符
      if (name === '|') {
        editor.$toolbar.append($('<div class="divider"></div>'));
        return;
      }

      // 插入 spacer
      if (name === ' ') {
        editor.$toolbar.append($('<div class="mdui-toolbar-spacer"></div>'));
        return;
      }

      const MenuConstructor = MenuConstructors[name];
      if (MenuConstructor && typeof MenuConstructor === 'function') {
        // 实例化按钮
        this.menus[name] = new MenuConstructor(editor);
        const menu = this.menus[name];

        // 添加到工具栏
        menu.$button = $(`<button class="mdui-btn menu menu-${name}" type="button" title="${menu.title}"><i class="mdui-icon material-icons">${menu.icon}</i></button>`)
          .appendTo(editor.$toolbar)
          .on('click', () => {
            if (editor.selection.getRange() === null) {
              return;
            }

            menu.onclick();
          });
      }
    });
  },

  /**
   * 修改菜单按钮状态
   */
  changeStatus() {
    const editor = this.editor;
    let disableMenus = [];

    $.each(this.menus, (name, menu) => {
      setTimeout(() => {
        // 切换激活状态
        if (menu.isActive) {
          if (menu.isActive()) {
            menu.$button.addClass('active');

            if (menu.disable) {
              disableMenus = disableMenus.concat(menu.disable);
            }
          } else {
            menu.$button.removeClass('active');
          }
        }

        // 禁用按钮
        if (name === editor.options.menus[editor.options.menus.length - 1]) {
          disableMenus = $.unique(disableMenus);
          $.each(this.menus, (_name, _menu) => {
            _menu.$button.prop('disabled', disableMenus.indexOf(_name) > -1);
          });
        }
      }, 0);
    });
  },
};

export default Menus;
