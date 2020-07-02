import { h } from 'hyperapp';
import cc from 'classcat';
import { storageTypeMap } from '~/pages/options/dataMap';

import Input from '../../components/input/view.jsx';
import Select from '../../components/select/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Input
        label="上传文件目录外部访问 URL 地址"
        name="storage_url"
        value={data.storage_url}
        helper="留空时将使用 /public/static/upload 目录下的资源"
      />
      <Select
        label="存储类型"
        name="storage_type"
        value={data.storage_type}
        data={storageTypeMap}
        onChange={(e) => {
          data.storage_type = e.target.value;
          actions.setState({ data });
        }}
      />
      <div class={cc([{ 'mdui-hidden': data.storage_type !== 'local' }])}>
        <Input
          label="本地文件存储目录绝对路径"
          name="storage_local_dir"
          value={data.storage_local_dir}
        />
      </div>
      <div class={cc([{ 'mdui-hidden': data.storage_type !== 'ftp' }])}>
        <Select
          label="协议"
          name="storage_ftp_ssl"
          value={data.storage_ftp_ssl}
          data={{ true: 'FTPS', false: 'FTP' }}
        />
        <Input
          label="FTP 服务器地址"
          name="storage_ftp_host"
          value={data.storage_ftp_host}
        />
        <Input
          label="端口"
          name="storage_ftp_port"
          value={data.storage_ftp_port}
        />
        <Input
          label="用户名"
          name="storage_ftp_username"
          value={data.storage_ftp_username}
        />
        <Input
          label="密码"
          name="storage_ftp_password"
          value={data.storage_ftp_password}
        />
        <Input
          label="上传目录"
          name="storage_ftp_root"
          value={data.storage_ftp_root}
          helper="例如：/path/to/root"
        />
        <Select
          label="传输模式"
          name="storage_ftp_passive"
          value={data.storage_ftp_passive}
          data={{ true: '被动模式', false: '主动模式' }}
        />
      </div>
      <div class={cc([{ 'mdui-hidden': data.storage_type !== 'sftp' }])}>
        <Input
          label="SFTP 服务器地址"
          name="storage_sftp_host"
          value={data.storage_sftp_host}
        />
        <Input
          label="端口"
          name="storage_sftp_port"
          value={data.storage_sftp_port}
        />
        <Input
          label="用户名"
          name="storage_sftp_username"
          value={data.storage_sftp_username}
        />
        <Input
          label="密码"
          name="storage_sftp_password"
          value={data.storage_sftp_password}
        />
        <Input
          label="上传目录"
          name="storage_sftp_root"
          value={data.storage_sftp_root}
          helper="例如：/path/to/root"
        />
      </div>
      <div class={cc([{ 'mdui-hidden': data.storage_type !== 'aliyun' }])}>
        <Input
          label="AccessKey ID"
          name="storage_aliyun_access_id"
          value={data.storage_aliyun_access_id}
        />
        <Input
          label="Access Key Secret"
          name="storage_aliyun_access_secret"
          value={data.storage_aliyun_access_secret}
        />
        <Input
          label="Bucket 名称"
          name="storage_aliyun_bucket"
          value={data.storage_aliyun_bucket}
        />
        <Input
          label="EndPoint（地域节点）"
          name="storage_aliyun_endpoint"
          value={data.storage_aliyun_endpoint}
        />
      </div>
      <div class={cc([{ 'mdui-hidden': data.storage_type !== 'upyun' }])}>
        <Input
          label="服务名称"
          name="storage_upyun_bucket"
          value={data.storage_upyun_bucket}
        />
        <Input
          label="操作员账号"
          name="storage_upyun_operator"
          value={data.storage_upyun_operator}
        />
        <Input
          label="操作员密码"
          name="storage_upyun_password"
          value={data.storage_upyun_password}
        />
      </div>
      <div class={cc([{ 'mdui-hidden': data.storage_type !== 'qiniu' }])}>
        <Input
          label="AccessKey"
          name="storage_qiniu_access_id"
          value={data.storage_qiniu_access_id}
        />
        <Input
          label="SecretKey"
          name="storage_qiniu_access_secret"
          value={data.storage_qiniu_access_secret}
        />
        <Input
          label="存储空间名称"
          name="storage_qiniu_bucket"
          value={data.storage_qiniu_bucket}
        />
        <Select
          label="存储区域"
          name="storage_qiniu_zone"
          value={data.storage_qiniu_zone}
          data={{
            z0: '华东',
            z1: '华北',
            z2: '华南',
            na0: '北美',
            as0: '东南亚',
          }}
        />
      </div>
      <SaveBtn submitting={submitting} />
    </form>
  );
};
