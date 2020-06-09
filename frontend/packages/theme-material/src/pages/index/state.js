import extend from 'mdui.jq/es/functions/extend';
import topicsState from './components/topics/state';

const as = {
  topics_data: [],
  topics_pagination: null,
  topics_loading: false,

  questions_recent_data: [],
  questions_recent_pagination: null,
  questions_recent_loading: false,

  questions_popular_data: [],
  questions_popular_pagination: null,
  questions_popular_loading: false,

  articles_recent_data: [],
  articles_recent_pagination: null,
  articles_recent_loading: false,

  articles_popular_data: [],
  articles_popular_pagination: null,
  articles_popular_loading: false,
};

export default extend(as, topicsState);
