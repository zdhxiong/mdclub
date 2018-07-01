import $ from 'mdui.JQ';
import { replaceHtmlSymbol } from '../helper/utils';

function Code(editor) {
  this.editor = editor;
  this.icon = 'code';
  this.title = '代码块';
  this.disable = ['bold', 'italic', 'head', 'ol', 'ul', 'link', 'image'];
  this._active = false;
  this._init();
}

Code.prototype = {
  constructor: Code,

  _init() {
    const editor = this.editor;

    editor.$content
      .on('keydown', (event) => {
        if (event.keyCode === 13) {
          // 按回车时，添加 \n
          if (this._active) {
            event.preventDefault();

            const _startOffset = editor.selection.getRange().startOffset;

            editor.cmd.do('insertHTML', '\n');
            editor.selection.saveRange();
            if (editor.selection.getRange().startOffset === _startOffset) {
              // 没起作用，再来一次
              editor.cmd.do('insertHTML', '\n');
            }

            // 换行后滚动条回到最左侧
            editor.selection.getContainerElem()[0].scrollLeft = 0;
          }
        }

        if (event.keyCode === 9) {
          // 按 tab 时，添加四个空格
          if (this._active) {
            event.preventDefault();
            editor.cmd.do('insertHTML', '    ');
          }
        }
      });
  },

  onclick() {
    const editor = this.editor;
    const $rootElem = editor.selection.getRootElem();

    if (this._active) {
      // 若当前是代码块，则每一行都转换为 p 标签
      const textArray = $rootElem.text().split('\n');
      let html = '';

      textArray.forEach((_line) => {
        const line = replaceHtmlSymbol(_line);
        html = line ? `<p>${line}</p>${html}` : `<p><br></p>${html}`;
      });

      editor.cmd.do('replaceRoot', html);

      return;
    }

    if (!$rootElem.length) {
      const range = editor.selection.getRange();

      if (range.collapsed) {
        // 没有选中任何选区，在最后添加一行
        editor.cmd.do('appendHTML', '<pre><br></pre>');
      } else {
        // 选中了多行，把多行包裹在同一个 pre 中
        let text = '';
        let isInRange = false;
        let $linesRemove = $();

        editor.$content.children().each((i, line) => {
          const $line = $(line);

          if (!isInRange) {
            if (
              $line.is(range.startContainer) ||
              $line[0].contains(range.startContainer) ||
              editor.$content.is(range.startContainer)
            ) {
              isInRange = true;
            }
          }

          if (isInRange) {
            text += `${replaceHtmlSymbol($line.text())}\n`;
            $linesRemove = $linesRemove.add($line);

            if ($line.is(range.endContainer) || $line[0].contains(range.endContainer)) {
              return false;
            }
          }

          return true;
        });


        $linesRemove.each((i, line) => {
          const $line = $(line);
          if (i < $linesRemove.length - 1) {
            $line.remove();
          }
        });

        editor.selection.createRangeByElem($linesRemove.last(), false, true);
        editor.cmd.do('replaceRoot', `<pre>${text}</pre>`);
      }

      return;
    }

    // 选中单行，需要移除选区内容所有子元素的标签，然后转换为 pre
    const text = replaceHtmlSymbol($rootElem.text());
    editor.cmd.do('replaceRoot', text ? `<pre>${text}</pre>` : '<pre><br></pre>');
  },

  isActive() {
    this._active = this.editor.selection.getRootElem().is('pre');

    return this._active;
  },
};

export default Code;
