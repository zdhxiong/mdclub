import '~/vendor/mdui';
import SDKConfig from 'mdclub-sdk-js/es/defaults';
import SDKAdapter from 'mdclub-sdk-js/es/adapter/BrowserAdapter';
import { isIE } from 'mdui.jq/es/utils';
import { setCookie } from '~/utils/cookie';
import './index.less';

// 设置 mdclub-sdk-js
SDKConfig.apiPath = window.G_API;
SDKConfig.adapter = new SDKAdapter();
SDKConfig.error = () => {};

// 把 token 写入 cookie
const token = SDKConfig.adapter.getStorage('token');
if (token) {
  setCookie('token', token);
}

// 控制台安全提示
if (window.top === window && window.console && !isIE()) {
  setTimeout(() => {
    window.console.log(
      '%c%s',
      'color: red; background: yellow; font-size: 24px;',
      '警告！',
    );
    window.console.log(
      '%c%s',
      'color: black; font-size: 18px;',
      `使用此控制台可能会让攻击者利用 Self-XSS（自跨站脚本）攻击来冒充你，并窃取你的信息。
请勿输入或粘贴你不明白的代码。`,
    );
  });
}
