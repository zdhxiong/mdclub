import highlight from './highlight';

export default html => (element) => {
  if (!html || html.replace(/ /ig, '') === '<p><br/></p>') {
    html = '';
  }

  element.innerHTML = html;
  highlight(element);
};
