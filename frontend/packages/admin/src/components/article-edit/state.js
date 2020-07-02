import extend from 'mdui.jq/es/functions/extend';
import editorState from '~/components/editor/state';
import topicSelectorState from '~/components/editor/components/topic-selector/state';

export default extend({}, editorState, topicSelectorState);
