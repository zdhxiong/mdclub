export default {
  topic: {
    fields: [
      {
        name: 'topic_id',
        label: '话题ID',
      },
      {
        name: 'name',
        label: '话题名称',
      },
    ],
    topic_id: '',
    name: '',
  },
  user: {
    fields: [
      {
        name: 'user_id',
        label: '用户ID',
      },
      {
        name: 'username',
        label: '用户名',
      },
      {
        name: 'email',
        label: '邮箱',
      },
    ],
    user_id: '',
    username: '',
    email: '',
  },
  question: {
    fields: [
      {
        name: 'question_id',
        label: '提问ID',
      },
      {
        name: 'user_id',
        label: '用户ID',
      },
    ],
    question_id: '',
    user_id: '',
  },
  answer: {
    fields: [
      {
        name: 'answer_id',
        label: '回答ID',
      },
      {
        name: 'question_id',
        label: '提问ID',
      },
      {
        name: 'user_id',
        label: '用户ID',
      },
    ],
    answer_id: '',
    question_id: '',
    user_id: '',
  },
  article: {
    fields: [
      {
        name: 'article_id',
        label: '文章ID',
      },
      {
        name: 'user_id',
        label: '用户ID',
      },
    ],
    article_id: '',
    user_id: '',
  },
  comment: {
    fields: [
      {
        name: 'comment_id',
        label: '评论ID',
      },
      {
        name: 'commentable_id',
        label: '评论目标ID'
      },
      {
        name: 'commentable_type',
        label: '评论目标类型',
        enum: [
          {
            name: '文章',
            value: 'article',
          },
          {
            name: '提问',
            value: 'question',
          },
          {
            name: '回答',
            value: 'answer',
          },
        ],
      },
      {
        name: 'user_id',
        label: '用户ID',
      },
    ],
    comment_id: '',
    commentable_id: '',
    commentable_type: '',
    user_id: '',
  },
  image: {
    fields: [
      {
        name: 'hash',
        label: 'hash',
      },
      {
        name: 'item_type',
        label: '类型',
        enum: [
          {
            name: '文章',
            value: 'article',
          },
          {
            name: '提问',
            value: 'question',
          },
          {
            name: '回答',
            value: 'answer',
          },
        ],
      },
      {
        name: 'item_id',
        label: '类型ID',
      },
      {
        name: 'user_id',
        label: '用户ID',
      },
    ],
    hash: '',
    item_type: '',
    item_id: '',
    user_id: '',
  },
  report: {
    fields: [
      {
        name: 'reportable_type',
        label: '类型',
        enum: [
          {
            name: '文章',
            value: 'article',
          },
          {
            name: '提问',
            value: 'question',
          },
          {
            name: '回答',
            value: 'answer',
          },
          {
            name: '评论',
            value: 'comment',
          },
          {
            name: '用户',
            value: 'user',
          },
        ],
      },
    ],
    reportable_type: '',
  },
};
