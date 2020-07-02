import highlight from '~/vendor/highlight/index';

/**
 * 富文本，包含代码高亮
 */
export const richText = (html) => (element) => {
  if (!html || html.replace(/ /gi, '') === '<p><br/></p>') {
    html = '';
  }

  element.innerHTML = html;
  highlight(element);
};

/**
 * 移除 HTML 标签，移除换行符，返回纯文本。用于显示富文本摘要或单行纯文本
 * @param str 字符串
 * @param length 截取的字符串长度，默认不截取
 */
export const summaryText = (str, length = 0) => (element) => {
  let text = str.replace(/<[^>]+>/g, '');

  if (length) {
    text = text.substr(0, length);
  }

  element.innerHTML = text;
};

/**
 * 纯文本，把换行符转换为 <p>
 * @param str
 */
export const plainText = (str) => (element) => {
  element.innerHTML = `<p>${str.replace(/[\r\n]/g, '</p><p>')}</p>`;
};
