/**
 * 为链接补充前缀
 * @param url
 */
function fullPath(url) {
  // @ts-ignore
  return window.G_ADMIN_ROOT + url;
}

function matchString(url) {
  return window.location.pathname === fullPath(url);
}

function matchRegular(regular) {
  const path = window.location.pathname.substr(window.G_ADMIN_ROOT.length);

  return regular.test(path);
}

function isPathIndex() {
  return matchString('');
}

function isPathArticles() {
  return matchString('/articles');
}

function isPathQuestions() {
  return matchString('/questions');
}

function isPathAnswers() {
  return matchString('/answers');
}

function isPathTopics() {
  return matchString('/topics');
}

function isPathUsers() {
  return matchString('/users');
}

function isPathComments() {
  return matchString('/comments');
}

function isPathReports() {
  return matchString('/reports');
}

function isPathOption() {
  return matchRegular(/^\/options\/\w+$/);
}

function isPathOptions() {
  return matchString('/options');
}

export {
  fullPath,
  isPathIndex,
  isPathArticles,
  isPathQuestions,
  isPathAnswers,
  isPathTopics,
  isPathUsers,
  isPathComments,
  isPathReports,
  isPathOption,
  isPathOptions,
};
