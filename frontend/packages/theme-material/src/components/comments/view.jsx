import { h } from 'hyperapp';
import './index.less';

import Loading from '~/components/loading/view.jsx';
import Loaded from '~/components/loaded/view.jsx';
import Empty from '~/components/empty/view.jsx';
import ListHeader from '~/components/list-header/view.jsx';
import Item from './components/item/view.jsx';
import NewComment from './components/new-comment/view.jsx';

export default ({
  commentable_type,
  commentable_id,
  isInDialog,
  state,
  actions,
}) => {
  const { order, comments_data, pagination, loading, submitting } = state;

  const isEmpty = !loading && pagination && !pagination.total;

  let count = 0;
  if (pagination) {
    count = pagination.total;
  }

  return (
    <div class="mc-comments">
      <ListHeader
        show={true}
        title={`共 ${count} 条评论`}
        disabled={loading || submitting}
        currentOrder={order}
        key="comments"
        orders={[
          {
            order: 'create_time',
            name: '发布时间（从早到晚）',
          },
          {
            order: '-create_time',
            name: '发布时间（从晚到早）',
          },
          {
            order: '-vote_count',
            name: '最热门',
          },
        ]}
        onChangeOrder={actions.changeOrder}
        closeBtnClick={isInDialog ? actions.closeDialog : false}
      />
      <div
        class="comments-wrapper"
        oncreate={() => {
          actions.onCreate({ commentable_type, commentable_id, isInDialog });
        }}
        ondestroy={() => actions.onDestroy({ isInDialog })}
      >
        <Empty show={isEmpty} title="尚未有人发表评论" />

        <If condition={comments_data && comments_data.length}>
          <div class="mdui-card comments">
            {comments_data.map((comment, commentIndex) => (
              <Item
                comment={comment}
                commentIndex={commentIndex}
                actions={actions}
              />
            ))}
          </div>
        </If>

        <Loading show={loading} />
        <Loaded
          show={!loading && pagination && pagination.page === pagination.pages}
        />
      </div>

      <If condition={isInDialog}>
        <NewComment submitting={state.submitting} onSubmit={actions.onSubmit} />
      </If>
    </div>
  );
};
