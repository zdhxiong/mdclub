import { h } from 'hyperapp';
import cc from 'classcat';
import { searchTypeMap } from '~/pages/options/dataMap';

import Select from '../../components/select/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Select
        label="搜索引擎"
        name="search_type"
        value={data.search_type}
        data={searchTypeMap}
        onChange={(e) => {
          data.search_type = e.target.value;
          actions.setState({ data });
        }}
      />
      <div class={cc([{ 'mdui-hidden': data.search_type !== 'third' }])}>
        <Select
          label="第三方搜索引擎名称"
          name="search_third"
          value={data.search_third}
          data={{
            google: 'Google',
            bing: 'Bing',
            sogou: '搜狗',
            360: '360',
            baidu: '百度',
          }}
        />
      </div>
      <SaveBtn submitting={submitting} />
    </form>
  );
};
