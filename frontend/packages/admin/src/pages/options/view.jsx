import { h } from 'hyperapp';
import { Link } from 'hyperapp-router';
import { fullPath } from '~/utils/path';
import { cacheTypeMap, storageTypeMap, searchTypeMap } from './dataMap';
import './index.less';

import Loading from '~/components/loading/view.jsx';

const Item = ({ to, label, value = null }) => (
  <Link to={fullPath(to)} class="item mdui-ripple">
    <div class="label mdui-text-color-theme-text">{label}</div>
    <If condition={value}>
      <div class="value mdui-text-color-theme-secondary">{value}</div>
    </If>
    <i class="arrow mdui-icon material-icons mdui-text-color-theme-icon">
      arrow_drop_down
    </i>
  </Link>
);

const timeIn = (ts) => {
  if (ts < 60) {
    return `${ts} 秒内`;
  }

  if (ts < 3600) {
    return `${ts / 60} 分钟内`;
  }

  if (ts < 86400) {
    return `${ts / 3600} 小时内`;
  }

  return `${ts / 86400} 天内`;
};

export default (state, actions) => ({ match }) => {
  const { data, loading } = state;

  return (
    <div
      oncreate={(element) => actions.onCreate({ element })}
      ondestroy={(element) => actions.onDestroy({ element })}
      key={match.url}
      class="mdui-container"
      id="page-options"
    >
      <Loading show={loading} />
      <If condition={data}>
        <div class="subheader subheader-first mdui-text-color-theme-secondary">
          系统设置
        </div>
        <div class="mdui-card mdui-card-shadow">
          <Item to="/options/info" label="站点信息" value={data.site_name} />
          <Item to="/options/theme" label="主题" value={data.theme} />
          <Item to="/options/mail" label="邮件" value={data.smtp_host} />
          <Item
            to="/options/cache"
            label="缓存"
            value={cacheTypeMap[data.cache_type]}
          />
          <Item
            to="/options/search"
            label="搜索引擎"
            value={searchTypeMap[data.search_type]}
          />
          <Item
            to="/options/upload"
            label="文件上传"
            value={storageTypeMap[data.storage_type]}
          />
        </div>

        <div class="subheader mdui-text-color-theme-secondary">
          编辑与删除权限设置
        </div>
        <div class="mdui-card mdui-card-shadow">
          <Item
            to="/options/article_edit"
            label="文章编辑"
            value={
              // eslint-disable-next-line no-nested-ternary
              data.article_can_edit
                ? [
                    data.article_can_edit_only_no_comment ? '无评论' : null,
                    data.article_can_edit_before
                      ? timeIn(data.article_can_edit_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/article_delete"
            label="文章删除"
            value={
              data.article_can_delete
                ? [
                    data.article_can_delete_only_no_comment ? '无评论' : null,
                    data.article_can_delete_before
                      ? timeIn(data.article_can_delete_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/question_edit"
            label="提问编辑"
            value={
              data.question_can_edit
                ? [
                    data.question_can_edit_only_no_answer ? '无回答' : null,
                    data.question_can_edit_only_no_comment ? '无评论' : null,
                    data.question_can_edit_before
                      ? timeIn(data.question_can_edit_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/question_delete"
            label="提问删除"
            value={
              data.question_can_delete
                ? [
                    data.question_can_delete_only_no_answer ? '无回答' : null,
                    data.question_can_delete_only_no_comment ? '无评论' : null,
                    data.question_can_delete_before
                      ? timeIn(data.question_can_delete_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/answer_edit"
            label="回答编辑"
            value={
              data.answer_can_edit
                ? [
                    data.answer_can_edit_only_no_comment ? '无回答' : null,
                    data.answer_can_edit_before
                      ? timeIn(data.answer_can_edit_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/answer_delete"
            label="回答删除"
            value={
              data.answer_can_delete
                ? [
                    data.answer_can_delete_only_no_comment ? '无回答' : null,
                    data.answer_can_delete_before
                      ? timeIn(data.answer_can_delete_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/comment_edit"
            label="评论编辑"
            value={
              data.comment_can_edit
                ? [
                    data.comment_can_edit_before
                      ? timeIn(data.comment_can_edit_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
          <Item
            to="/options/comment_delete"
            label="评论删除"
            value={
              data.comment_can_delete
                ? [
                    data.comment_can_delete_before
                      ? timeIn(data.comment_can_delete_before)
                      : null,
                  ]
                    .filter((x) => x)
                    .join(' 且 ')
                : '禁止'
            }
          />
        </div>
      </If>
    </div>
  );
};
