import $ from 'mdui.jq';
import mdui from 'mdui';
import { get as getUserInfo } from 'mdclub-sdk-js/es/UserApi';
import { findIndex } from '~/utils/func';
import apiCatch from '~/utils/errorHandler';

export default {
  /**
   * @param element
   * @param dataName 数据或数据列表的字段名
   * @param primaryKey 若是数据列表，则需要提供主键字段名
   * @param primaryValue 若是数据列表，则需要提供主键字段值
   * @param actions
   */
  onUserPopoverCreate: ({
    element,
    dataName,
    primaryKey = null,
    primaryValue = null,
  }) => (state, actions) => {
    const $element = $(element);
    const $trigger = $element.find('.user-popover-trigger');
    const $popover = $element.find('.popover');
    const menu = new mdui.Menu($trigger, $popover);

    let timeoutId;

    $trigger
      .on('mouseenter', () => {
        timeoutId = setTimeout(() => {
          menu.open();

          actions.loadUserForUserPopover({
            dataName,
            primaryKey,
            primaryValue,
          });
        }, 500);
      })
      .on('mouseleave', () => {
        clearTimeout(timeoutId);
        timeoutId = null;
      });

    $popover.on('mouseleave', () => {
      if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
      }

      menu.close();
    });
  },

  /**
   * 加载用户详细信息
   * @param dataName
   * @param primaryKey
   * @param primaryValue
   */
  loadUserForUserPopover: ({ dataName, primaryKey, primaryValue }) => (
    state,
    actions,
  ) => {
    // eslint-disable-next-line no-eval
    const data = eval(`state.${dataName}`);

    let index;
    let item;
    if (primaryKey) {
      index = findIndex(data, primaryKey, primaryValue);
      item = data[index];
    }

    const user_id = primaryKey
      ? item.relationships.user.user_id
      : data.relationships.user.user_id;

    getUserInfo({
      user_id,
      include: ['is_followed', 'is_following', 'is_me'],
    })
      .then((response) => {
        if (primaryKey) {
          data[index].relationships.user = response.data;
        } else {
          data.relationships.user = response.data;
        }

        actions.setState({ [dataName]: data });
      })
      .catch(apiCatch);
  },
};
