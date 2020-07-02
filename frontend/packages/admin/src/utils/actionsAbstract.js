import $ from 'mdui.jq';
import { loadEnd } from '~/utils/loading';
import { apiCatch } from '~/utils/errorHandlers';
import {
  isPathAnswers,
  isPathArticles,
  isPathComments,
  isPathQuestions,
  isPathReports,
  isPathTopics,
} from '~/utils/path';

/**
 * 所有 actions 通用
 */
export default {
  /**
   * 设置状态
   */
  setState: (value) => value,

  /**
   * 获取状态
   */
  getState: () => (state) => state,

  /**
   * 设置网页 title
   */
  setTitle: (title) => {
    $('title').text(title);
  },

  /**
   * 删除成功后，重新载入数据
   */
  deleteSuccess: () => {
    loadEnd();

    let actionsTarget;

    if (isPathQuestions()) {
      actionsTarget = window.app.questions;
    } else if (isPathArticles()) {
      actionsTarget = window.app.articles;
    } else if (isPathAnswers()) {
      actionsTarget = window.app.answers;
    } else if (isPathComments()) {
      actionsTarget = window.app.comments;
    } else if (isPathTopics()) {
      actionsTarget = window.app.topics;
    } else if (isPathReports()) {
      actionsTarget = window.app.reports;
    }

    actionsTarget.loadData();
  },

  /**
   * 删除失败
   */
  deleteFail: (response) => {
    loadEnd();
    apiCatch(response);
  },
};
