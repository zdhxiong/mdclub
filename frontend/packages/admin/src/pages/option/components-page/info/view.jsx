import { h } from 'hyperapp';
import { languageTypeMap } from '~/pages/options/dataMap';

import Input from '../../components/input/view.jsx';
import Textarea from '../../components/textarea/view.jsx';
import Select from '../../components/select/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Input label="网站名称" name="site_name" value={data.site_name} />
      <Textarea
        label="网站简介"
        name="site_description"
        value={data.site_description}
      />
      <Textarea
        label="网站关键词"
        name="site_keywords"
        helper="多个关键词之间用半角逗号隔开"
        value={data.site_keywords}
      />
      <Input
        label="ICP 备案号"
        name="site_icp_beian"
        helper="例如：京ICP证030173号"
        value={data.site_icp_beian}
      />
      <Input
        label="公安备案号"
        name="site_gongan_beian"
        helper="例如：京公网安备 11000002000001 号"
        value={data.site_gongan_beian}
      />
      <Select
        label="语言"
        name="language"
        value={data.language}
        data={languageTypeMap}
      />
      <Input
        label="public/static 目录外部访问 url 地址"
        name="site_static_url"
        helper="可为 public/static 目录绑定独立域名。例如：https://cdn.mdclub.org/static/"
        value={data.site_static_url}
      />
      <SaveBtn submitting={submitting} />
    </form>
  );
};
