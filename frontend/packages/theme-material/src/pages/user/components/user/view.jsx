import { h } from 'hyperapp';
import cc from 'classcat';
import $ from 'mdui.jq';
import { $window } from 'mdui/es/utils/dom';
import { emit } from '~/utils/pubsub';
import { summaryText, plainText } from '~/utils/html';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import AvatarUpload from '~/components/avatar-upload/view.jsx';
import CoverUpload from '~/components/cover-upload/view.jsx';
import Follow from '~/components/follow/view.jsx';
import OptionsButton from '~/components/options-button/view.jsx';

const MetaItem = ({ icon, text, tooltip, multiLine = false }) => {
  const contentFunc = multiLine ? plainText(text) : summaryText(text);

  return (
    <div class="meta">
      <i
        class="mdui-icon material-icons mdui-text-color-theme-icon"
        mdui-tooltip={`{content: '${tooltip}', delay: 300}`}
      >
        {icon}
      </i>
      <div
        class={cc([{ 'mdui-typo': multiLine }])}
        oncreate={contentFunc}
        onupdate={contentFunc}
      />
    </div>
  );
};

export default ({ state, actions, is_me }) => {
  const { interviewee } = state;

  return (
    <div key="user" class="user mdui-card mdui-card-shadow">
      <Loading show={state.loading} />
      <If condition={interviewee}>
        <div
          class="cover"
          style={{
            backgroundImage: `url("${interviewee.cover.large}")`,
          }}
          oncreate={(element) => {
            // cover 元素创建完成后，绑定滚动事件，使封面随着滚动条滚动
            setTimeout(() => {
              $window.on('scroll', () => {
                window.requestAnimationFrame(() => {
                  element.style['background-position-y'] = `${
                    window.pageYOffset / 2
                  }px`;
                });
              });

              // 向下滚动一段距离
              window.scrollTo(0, $(element).width() * 0.56 * 0.58);
            });
          }}
        >
          <If condition={is_me}>
            <CoverUpload user={interviewee} />
          </If>
        </div>
        <div class="info">
          <div class="avatar-box">
            <If condition={is_me}>
              <AvatarUpload user={interviewee} />
            </If>
            <img src={interviewee.avatar.large} class="avatar" />
          </div>
          <div
            class={cc([
              'profile',
              {
                fold: state.profile_fold,
              },
            ])}
          >
            <div class="meta username mdui-text-color-theme-text">
              {interviewee.username}
            </div>
            <If condition={interviewee.headline}>
              <MetaItem
                icon="credit_card"
                text={interviewee.headline}
                tooltip="一句话介绍"
              />
            </If>
            <If condition={interviewee.blog}>
              <MetaItem
                icon="insert_link"
                text={interviewee.blog}
                tooltip="个人主页"
              />
            </If>
            <If condition={interviewee.company}>
              <MetaItem
                icon="location_city"
                text={interviewee.company}
                tooltip="所属学校或企业"
              />
            </If>
            <If condition={interviewee.location}>
              <MetaItem
                icon="location_on"
                text={interviewee.location}
                tooltip="所在地区"
              />
            </If>
            <If condition={interviewee.bio}>
              <MetaItem
                icon="description"
                text={interviewee.bio}
                tooltip="个人简介"
                multiLine={true}
              />
            </If>
            <If
              condition={
                interviewee.blog ||
                interviewee.company ||
                interviewee.location ||
                interviewee.bio
              }
            >
              <button
                class="fold-button mdui-btn"
                onclick={() => {
                  actions.setState({ profile_fold: !state.profile_fold });
                }}
              >
                <i class="mdui-icon-left mdui-icon material-icons mdui-text-color-theme-icon">
                  {state.profile_fold
                    ? 'keyboard_arrow_down'
                    : 'keyboard_arrow_up'}
                </i>
                {state.profile_fold ? '查看' : '收起'}详细资料
              </button>
            </If>
          </div>
        </div>
        <div class="actions">
          <If condition={!is_me}>
            <Follow
              item={interviewee}
              type="user"
              id={interviewee.user_id}
              actions={actions}
            />
          </If>
          <If condition={is_me}>
            <button
              class="edit mdui-btn mdui-btn-icon mdui-btn-outlined"
              mdui-tooltip="{content: '编辑个人资料', delay: 300}"
              onclick={actions.updateUserInfo}
            >
              <i class="mdui-icon material-icons mdui-text-color-theme-icon">
                edit
              </i>
            </button>
          </If>
          <div class="follow">
            <button
              class="followers mdui-btn mdui-text-color-theme-secondary"
              onclick={() => {
                emit('users_dialog_open', {
                  type: 'followers',
                  id: interviewee.user_id,
                });
              }}
            >
              {interviewee.follower_count} 人关注
            </button>
            <div class="divider" />
            <button
              class="followees mdui-btn mdui-text-color-theme-secondary"
              onclick={() => {
                emit('users_dialog_open', {
                  type: 'followees',
                  id: interviewee.user_id,
                });
              }}
            >
              关注了 {interviewee.followee_count} 人
            </button>
          </div>
          <div class="flex-grow" />
          <OptionsButton
            type="user"
            item={interviewee}
            extraOptions={
              is_me
                ? [
                    {
                      name: '重置头像',
                      onClick: actions.deleteAvatar,
                    },
                    {
                      name: '重置封面',
                      onClick: actions.deleteCover,
                    },
                  ]
                : null
            }
          />
        </div>
      </If>
    </div>
  );
};
