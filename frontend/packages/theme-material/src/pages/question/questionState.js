import extend from 'mdui.jq/es/functions/extend';
import editorState from '~/components/editor/state';
import stateDefault from './stateDefault';

const as = {
  // 表示当前为提问页面
  route: 'question',
};

export default extend(as, stateDefault, editorState);
