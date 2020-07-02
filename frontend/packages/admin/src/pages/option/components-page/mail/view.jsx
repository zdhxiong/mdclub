import { h } from 'hyperapp';

import Input from '../../components/input/view.jsx';
import Select from '../../components/select/view.jsx';
import SaveBtn from '../../components/save-btn/view.jsx';

export default ({ state, actions }) => {
  const { data, submitting } = state;
  const { onSubmit, sendTestEmail } = actions;

  return (
    <form method="post" onsubmit={onSubmit}>
      <Input label="SMTP 服务器" name="smtp_host" value={data.smtp_host} />
      <Input
        label="SMTP 账户"
        name="smtp_username"
        value={data.smtp_username}
      />
      <Input
        label="SMTP 密码"
        name="smtp_password"
        value={data.smtp_password}
      />
      <Input label="SMTP 端口" name="smtp_port" value={data.smtp_port} />
      <Select
        label="加密连接类型"
        name="smtp_secure"
        value={data.smtp_secure}
        data={{ '': '无', tls: 'TLS', ssl: 'SSL' }}
      />
      <Input
        label="接收回复邮件的邮箱地址"
        name="smtp_reply_to"
        value={data.smtp_reply_to}
      />
      <SaveBtn submitting={submitting} />
      <button
        class="send-email mdui-btn mdui-btn-outlined mdui-color-theme mdui-ripple"
        type="button"
        onclick={sendTestEmail}
      >
        发送测试邮件
      </button>
    </form>
  );
};
