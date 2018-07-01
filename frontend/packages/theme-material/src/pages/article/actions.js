let global_actions;

export default {
  setState: value => (value),
  getState: () => state => state,

  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.theme.setPrimary('teal');
    global_actions.routeChange(window.location.pathname);
  },
};
