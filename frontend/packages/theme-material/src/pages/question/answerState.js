import extend from 'mdui.jq/es/functions/extend';
import editorState from '~/components/editor/state';
import stateDefault from './stateDefault';

const as = {
  // 表示当前为回答页面
  route: 'answer',
};

export default extend(as, stateDefault, editorState);
