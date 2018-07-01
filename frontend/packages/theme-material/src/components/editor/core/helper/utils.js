/**
 * 替换 html 特殊字符
 * @param html
 * @returns {string}
 */
export function replaceHtmlSymbol(html) {
  if (html == null) {
    return '';
  }
  return html
    .replace(/</gm, '&lt;')
    .replace(/>/gm, '&gt;')
    .replace(/"/gm, '&quot;');
}

/**
 * 为链接补充 http:// 前缀
 * @param _url
 * @returns {string}
 */
export function correctUrl(_url) {
  let url = _url.toLowerCase();
  if (
    url.indexOf('http://') < 0 &&
    url.indexOf('https://') < 0 &&
    url.indexOf('ftp://') < 0 &&
    url.indexOf('//') < 0
  ) {
    url = `http://${url}`;
  }

  return url;
}

/**
 * 获取粘贴的 HTML
 * @param e
 * @returns {string}
 */
export function getPasteHTML(e) {
  const clipboardData = e.clipboardData || (e.originalEvent && e.originalEvent.clipboardData);
  let pasteText;
  let pasteHtml;

  if (clipboardData == null) {
    pasteText = window.clipboardData && window.clipboardData.getData('text');
  } else {
    pasteText = clipboardData.getData('text/plain');
    pasteHtml = clipboardData.getData('text/html');
  }

  if (!pasteHtml && pasteText) {
    pasteText = replaceHtmlSymbol(pasteText);
    pasteHtml = pasteText ? `<p>${pasteText}</p>` : '<p><br></p>';
  }

  return pasteHtml;
}

/**
 * 获取粘贴的纯文本
 * @param e
 * @returns {string}
 */
export function getPasteText(e) {
  const clipboardData = e.clipboardData || (e.originalEvent && e.originalEvent.clipboardData);
  let pasteText;

  if (clipboardData == null) {
    pasteText = window.clipboardData && window.clipboardData.getData('text');
  } else {
    pasteText = clipboardData.getData('text/plain');
  }

  return replaceHtmlSymbol(pasteText);
}
