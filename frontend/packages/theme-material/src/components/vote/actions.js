import extend from 'mdui.jq/es/functions/extend';
import {
  addVote as addAnswerVote,
  deleteVote as deleteAnswerVote,
} from 'mdclub-sdk-js/es/AnswerApi';
import {
  addVote as addArticleVote,
  deleteVote as deleteArticleVote,
} from 'mdclub-sdk-js/es/ArticleApi';
import {
  addVote as addCommentVote,
  deleteVote as deleteCommentVote,
} from 'mdclub-sdk-js/es/CommentApi';
import { findIndex } from '~/utils/func';
import apiCatch from '~/utils/errorHandler';

const toggle = (id, commentIndex, type, voting, state, actions) => {
  let promise;

  // 文章详情页中
  if (type === 'article') {
    const { article } = state;
    const isDelete = article.relationships.voting === voting;

    article.relationships.voting = isDelete ? '' : voting;
    actions.setState({ article });

    promise = isDelete
      ? deleteArticleVote({ article_id: id })
      : addArticleVote({ article_id: id, type: voting });

    promise.then(({ data }) => {
      extend(article, data);
      actions.setState({ article });
    });
  }

  // 回答列表中
  if (type === 'answers') {
    const { answer_data } = state;
    const index = findIndex(answer_data, 'answer_id', id);
    const answer = answer_data[index];
    const isDelete = answer.relationships.voting === voting;

    answer.relationships.voting = isDelete ? '' : voting;
    answer_data[index] = answer;
    actions.setState({ answer_data });

    promise = isDelete
      ? deleteAnswerVote({ answer_id: id })
      : addAnswerVote({ answer_id: id, type: voting });

    promise.then(({ data }) => {
      extend(answer, data);
      answer_data[index] = answer;
      actions.setState({ answer_data });
    });
  }

  // 评论列表中
  if (type === 'comments') {
    const { comments_data } = state;
    const index = findIndex(comments_data, 'comment_id', id);
    const comment = comments_data[index];
    const isDelete = comment.relationships.voting === voting;

    comment.relationships.voting = isDelete ? '' : voting;
    comments_data[index] = comment;
    actions.setState({ comments_data });

    promise = isDelete
      ? deleteCommentVote({ comment_id: id })
      : addCommentVote({ comment_id: id, type: voting });

    promise.then(({ data }) => {
      extend(comment, data);
      comments_data[index] = comment;
      actions.setState({ comments_data });
    });
  }

  // 回复列表中
  if (type === 'replies') {
    const { comments_data } = state;
    const { replies_data } = comments_data[commentIndex];
    const index = findIndex(replies_data, 'comment_id', id);
    const reply = replies_data[index];
    const isDelete = reply.relationships.voting === voting;

    reply.relationships.voting = isDelete ? '' : voting;
    comments_data[commentIndex].replies_data[index] = reply;
    actions.setState({ comments_data });

    promise = isDelete
      ? deleteCommentVote({ comment_id: id })
      : addCommentVote({ comment_id: id, type: voting });

    promise.then(({ data }) => {
      extend(reply, data);
      comments_data[commentIndex].replies_data[index] = reply;
      actions.setState({ comments_data });
    });
  }

  promise.catch(apiCatch);
};

const getIdValue = (item, type) => {
  switch (type) {
    case 'article':
      return item.article_id;
    case 'answers':
      return item.answer_id;
    default:
      return item.comment_id;
  }
};

/**
 * 在需要用到投票的页面中，引入该 actions
 */
export default {
  /**
   * 点击赞同
   * @param item
   * @param type
   *              answers: 提问页面的回答列表
   *              article: 文章详情页
   *              comments: 评论列表
   *              replies: 评论回复列表
   * @param commentIndex 仅 replies 中需要，表示评论的索引号
   */
  voteUp: ({ item, type, commentIndex }) => (state, actions) => {
    toggle(getIdValue(item, type), commentIndex, type, 'up', state, actions);
  },

  /**
   * 点击不赞同
   * @param item
   * @param type
   *              answers: 提问页面的回答列表
   *              article: 文章详情页
   *              comments: 评论列表
   *              replies：评论回复列表
   * @param commentIndex 仅 replies 中需要，表示评论的索引号
   */
  voteDown: ({ item, type, commentIndex }) => (state, actions) => {
    toggle(getIdValue(item, type), commentIndex, type, 'down', state, actions);
  },
};
