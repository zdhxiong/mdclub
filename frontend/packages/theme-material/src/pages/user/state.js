import extend from 'mdui.jq/es/functions/extend';
import stateDefault from './stateDefault';

const state = {
  // 当前登录用户信息，为 null 时表示未登录
  user: null,
};

export default extend(state, stateDefault);
