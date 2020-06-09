import { h } from 'hyperapp';
import cc from 'classcat';
import $ from 'mdui.jq';
import { scrollHorizontal } from '~/utils/scroll';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import TopicItem from '~/components/topic-item/view.jsx';

const jumpEnd = (element, actions) => {
  actions.setState({
    topics_show_arrow_left: element.scrollLeft > 0,
    topics_show_arrow_right:
      element.scrollLeft + element.offsetWidth < element.scrollWidth,
  });
};

const jumpTo = (event, position, actions) => {
  const element = $(event.target).parents('.topics-wrapper').find('.topics')[0];

  const isLeft = position === 'left';
  const offset = element.offsetWidth * (isLeft ? -1 : 1);

  scrollHorizontal(element, {
    offset,
    callback: () => {
      jumpEnd(element, actions);
    },
  });
};

const Arrow = ({ icon, cls, show, onClick }) => (
  <button
    class={cc([
      'mdui-btn',
      'mdui-btn-raised',
      'mdui-btn-icon',
      'mdui-ripple',
      cls,
      {
        'mdui-hidden': !show,
      },
    ])}
    onclick={onClick}
  >
    <i class="mdui-icon material-icons mdui-text-color-theme-icon">{icon}</i>
  </button>
);

export default ({ state, actions }) => (
  <div class="topics-wrapper">
    <div class="topics" onupdate={(element) => jumpEnd(element, actions)}>
      {state.topics_data.map((topic) => (
        <TopicItem topic={topic} actions={actions} type="index_topics" />
      ))}
    </div>
    <Arrow
      icon="keyboard_arrow_left"
      cls="arrow-left"
      show={state.topics_show_arrow_left}
      onClick={(event) => jumpTo(event, 'left', actions)}
    />
    <Arrow
      icon="keyboard_arrow_right"
      cls="arrow-right"
      show={state.topics_show_arrow_right}
      onClick={(event) => jumpTo(event, 'right', actions)}
    />
    <Loading show={state.topics_loading} />
  </div>
);
