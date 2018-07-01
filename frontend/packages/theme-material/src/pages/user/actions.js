import mdui from 'mdui';
import $ from 'mdui.JQ';
import UserAvatarService from '../../service/UserAvatar';
import UserCoverService from '../../service/UserCover';
import UserInfoService from '../../service/UserInfo';
import UserFollowService from '../../service/UserFollow';

let global_actions;
let user_id;

export default {
  setState: value => (value),
  getState: () => state => state,
  setTitle: (title) => {
    $('title').text(title);
  },

  user: {
    /**
     * 设置用户头像
     */
    setAvatar: value => ({
      avatar: value,
    }),

    /**
     * 设置用户封面
     */
    setCover: value => ({
      cover: value,
    }),
  },

  /**
   * 初始化，由 oncreate 和 onupdate 生命周期函数触发。
   */
  init: props => (state, actions) => {
    global_actions = props.global_actions;
    global_actions.theme.setPrimary('');
    global_actions.routeChange(window.location.pathname);

    const this_user_id = props.user_id;
    if (this_user_id === user_id) {
      actions.setTitle(`${state.user.username}的个人主页`);
      return;
    }

    user_id = this_user_id;
    actions.loadInitData();
  },

  /**
   * cover 元素创建完成后，绑定滚动事件，使封面随着滚动条滚动
   */
  coverInit: (element) => {
    const $cover = $(element);

    $(window).on('scroll', () => {
      window.requestAnimationFrame(() => {
        $cover[0].style['background-position-y'] = `${window.pageYOffset / 2}px`;
      });
    });

    // 向下滚动一段距离
    window.scrollTo(0, 246);
  },

  /**
   * 加载当前被访问用户数据
   */
  loadInitData: () => (state, actions) => {
    // 访问自己的页面
    if (user_id === state.user.user_id) {
      actions.setTitle(`${state.user.username}的个人主页`);
      return;
    }

    // 从页面中加载用户数据
    const _user = window.G__USER;
    if (_user) {
      actions.setState({ _user });
      window.G__USER = null;
      actions.setTitle(`${_user.username}的个人主页`);

      return;
    }

    // ajax 加载用户数据
    actions.loadStart();
    actions.setState({
      _user: false,
    });

    const loaded = (response) => {
      actions.loadEnd();

      if (response.code) {
        mdui.snackbar(response.message);
        return;
      }

      actions.setState({
        _user: response.data,
      });
      actions.setTitle(`${response.data.username}的个人主页`);
    };

    UserInfoService.getOne(user_id, loaded);
  },

  /**
   * 删除头像
   */
  deleteAvatar: e => (state, actions) => {
    e.preventDefault();

    mdui.confirm(
      '系统将删除现有头像，并随机生成一个新头像。',
      '要删除现有头像吗？',
      () => {
        UserAvatarService.deleteMine((response) => {
          if (!response.code) {
            actions.user.setAvatar(response.data);

            return;
          }

          mdui.snackbar(response.message);
        });
      },
      () => {},
      {
        history: false,
        confirmText: '删除',
        cancelText: '取消',
      },
    );
  },

  /**
   * 删除封面
   */
  deleteCover: e => (state, actions) => {
    e.preventDefault();

    mdui.confirm(
      '系统将删除现有封面，并重置为默认封面。',
      '要删除现有封面吗？',
      () => {
        UserCoverService.deleteMine((response) => {
          if (!response.code) {
            actions.user.setCover(response.data);

            return;
          }

          mdui.snackbar(response.message);
        });
      },
      () => {},
      {
        history: false,
        confirmText: '删除',
        cancelText: '取消',
      },
    );
  },

  /**
   * 更新个人简介
   */
  updateHeadline: e => (state, actions) => {
    mdui.prompt('一句话介绍你自己', (headline) => {
      UserInfoService.updateMe(headline, (response) => {
        if (response.code) {
          mdui.snackbar(response.message);
          return;
        }

        actions.setState({
          user: response.data,
        });
      });
    }, () => {}, {
      confirmText: '提交',
      cancelText: '取消',
      history: false,
      type: 'textarea',
      maxlength: 40,
      defaultValue: state.user.headline,
    });
  },

  /**
   * 切换关注状态
   */
  toggleFollow: () => (state, actions) => {
    if (!global_actions.getState().user.user.user_id) {
      global_actions.components.login.open();

      return;
    }

    const user = actions.getState()._user;

    user.relationship.is_following = !user.relationship.is_following;
    actions.setState({
      _user: user,
    });

    const done = (response) => {
      if (!response.code) {
        global_actions.users.followUpdate();

        return;
      }

      mdui.snackbar(response.message);
      user.relationship.is_following = !user.relationship.is_following;
      actions.setState({
        _user: user,
      });
    };

    if (user.relationship.is_following) {
      UserFollowService.addFollow(user.user_id, done);
    } else {
      UserFollowService.deleteFollow(user.user_id, done);
    }
  },

  loadStart: () => ({
    loading: true,
  }),

  loadEnd: () => ({
    loading: false,
  }),
};
