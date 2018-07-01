/**
 * 净化器构造函数
 * @param editor
 * @constructor
 */
function Purifier(editor) {
  this.editor = editor;
}

Purifier.prototype = {
  constructor: Purifier,

  /**
   * 传入一个 HTML，或者 JQ 对象，返回处理干净的 HTML
   * @param html
   * @return string
   */
  do(html) {
    let result = '';

    // todo 目前直接返回用每一行都用 p 标签包裹的 html，后续开发根据白名单进行过滤
    html.split('\n').forEach((_line) => {
      // 移除行内的换行符
      const line = _line.replace(/[\r\n]/gm, '');

      result += line ? `<p>${line}</p>` : '<p><br></p>';
    });

    return result;
  },
};

export default Purifier;
