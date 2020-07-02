import { h } from 'hyperapp';
import cc from 'classcat';
import { cacheTypeMap } from '~/pages/options/dataMap';

import Input from '../../components/input/view.jsx';
import Select from '../../components/select/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Input
        label="缓存键名前缀"
        name="cache_prefix"
        value={data.cache_prefix}
      />
      <Select
        label="缓存类型"
        name="cache_type"
        value={data.cache_type}
        data={cacheTypeMap}
        onChange={(e) => {
          data.cache_type = e.target.value;
          actions.setState({ data });
        }}
      />
      <div class={cc([{ 'mdui-hidden': data.cache_type !== 'redis' }])}>
        <Input
          label="Redis 服务器地址"
          name="cache_redis_host"
          value={data.cache_redis_host}
        />
        <Input
          label="Redis 用户名"
          name="cache_redis_username"
          value={data.cache_redis_username}
        />
        <Input
          label="Redis 密码"
          name="cache_redis_password"
          value={data.cache_redis_password}
        />
        <Input
          label="Redis 端口号"
          name="cache_redis_port"
          value={data.cache_redis_port}
        />
      </div>
      <div class={cc([{ 'mdui-hidden': data.cache_type !== 'memcached' }])}>
        <Input
          label="Memcached 服务器地址"
          name="cache_memcached_host"
          value={data.cache_memcached_host}
        />
        <Input
          label="Memcached 用户名"
          name="cache_memcached_username"
          value={data.cache_memcached_username}
        />
        <Input
          label="Memcached 密码"
          name="cache_memcached_password"
          value={data.cache_memcached_password}
        />
        <Input
          label="Memcached 端口号"
          name="cache_memcached_port"
          value={data.cache_memcached_port}
        />
      </div>
      <SaveBtn submitting={submitting} />
    </form>
  );
};
