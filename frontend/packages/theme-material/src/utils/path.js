/**
 * 为链接补充前缀
 * @param url
 */
function fullPath(url) {
  // @ts-ignore
  return window.G_ROOT + url;
}

function matchString(url) {
  return window.location.pathname === fullPath(url);
}

function matchRegular(regular) {
  const path = window.location.pathname.substr(window.G_ROOT.length);

  return regular.test(path);
}

function isPathIndex() {
  return matchString('/');
}

function isPathArticle() {
  return matchRegular(/^\/articles\/\d+$/);
}

function isPathArticles() {
  return matchString('/articles');
}

function isPathQuestion() {
  return matchRegular(/^\/questions\/\d+$/);
}

function isPathQuestions() {
  return matchString('/questions');
}

function isPathTopic() {
  return matchRegular(/^\/topics\/\d+$/);
}

function isPathTopics() {
  return matchString('/topics');
}

function isPathUser() {
  return matchRegular(/^\/users\/\d+$/);
}

function isPathUsers() {
  return matchString('/users');
}

function isPathInbox() {
  return matchString('/inbox');
}

function isPathNotifications() {
  return matchString('/notifications');
}

export {
  fullPath,
  isPathIndex,
  isPathArticle,
  isPathArticles,
  isPathQuestion,
  isPathQuestions,
  isPathTopic,
  isPathTopics,
  isPathUser,
  isPathUsers,
  isPathInbox,
  isPathNotifications,
};
