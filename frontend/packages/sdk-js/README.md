# MDClub 的 Javascript 版 SDK

## 使用方法

### 通过 CDN 引入 JS 文件

注意：这种方式只适用于浏览器环境。

在 HTML 中通过 `<script>` 标签引入 JS 文件：

```html
<script
  src="https://cdn.jsdelivr.net/npm/mdclub-sdk-js@1.0.4/dist/mdclub-sdk.min.js"
  integrity="sha256-h4XtKOU7LD8xH509/AbdZxu/4LiRvtxAUaRFCjj6xOk="
  crossorigin="anonymous"
></script>
```

然后就可以编写 JS 代码了：

```js
// 设置 api 地址（可选，默认为 {当前域名}/api）
mdclubSDK.defaults.apiPath = 'https://example.com/api';

// 若浏览器不支持 patch, put, delete 请求，则需要将该选项设为 true
mdclubSDK.defaults.methodOverride = true;

// 设置全局回调函数（可选）
mdclubSDK.defaults.beforeSend = function () {};
mdclubSDK.defaults.success = function (response) {};
mdclubSDK.defaults.error = function (errMsg) {};
mdclubSDK.defaults.complete = function () {};

// 调用 SDK 方法
mdclubSDK.QuestionApi.getList({ question_id: 11 })
.then((questions) => {
  console.log(questions);
})
.catch((errMsg) => {
  console.log(errMsg);
});
```

### 在 ES6 模块化环境或小程序中使用

使用 `npm` 安装 SDK：

```bash
npm install mdclub-sdk-js --save
```

SDK 同时支持浏览器环境和小程序环境，你需要根据使用环境设置适配器。

若在小程序中使用，设置成小程序适配器。支持微信小程序、支付宝小程序、钉钉小程序、百度小程序。（注意：当前代码还未在小程序中进行过测试。）

```js
import MiniProgramAdapter from 'mdclubSDK/es/adapter/MiniProgramAdapter';
defaults.adapter = new MiniProgramAdapter();
```

若在浏览器中使用，设置成浏览器适配器。

```js
import BrowserAdapter from 'mdclubSDK/es/adapter/BrowserAdapter';
defaults.adapter = new BrowserAdapter();
```

若你的项目中已经通过 npm 安装了 jQuery、axios 之类的库，则可以设置对应的适配器，SDK 将直接使用对应的库提供的 ajax 方法。下面是所有支持的适配器：

| 适配器 | 使用方法 | 说明 |
| ---- | ---- | ---- |
| `AxiosAdapter` | `import AxiosAdapter from 'mdclubSDK/es/adapter/AxiosAdapter';`<br/>`defaults.adapter = new AxiosAdapter();` | 使用 [axios](https://github.com/axios/axios) 发送请求 |
| `BrowserAdapter` | `import BrowserAdapter from 'mdclubSDK/es/adapter/BrowserAdapter';`<br/>`defaults.adapter = new BrowserAdapter();` | 使用 [mdui.jq](https://github.com/zdhxiong/mdui.jq) 发送请求 |
| `FetchAdapter` | `import FetchAdapter from 'mdclubSDK/es/adapter/FetchAdapter';`<br/>`defaults.adapter = new FetchAdapter();` | 使用浏览器内置的 `fetch` 方法发送请求 |
| `JQueryAdapter` | `import JQueryAdapter from 'mdclubSDK/es/adapter/JQueryAdapter';`<br/>`defaults.adapter = new JQueryAdapter();` | 使用 [jQuery](https://github.com/jquery/jquery) 发送请求 |
| `MiniProgramAdapter` | `import MiniProgramAdapter from 'mdclubSDK/es/adapter/MiniProgramAdapter';`<br/>`defaults.adapter = new MiniProgramAdapter();` | 在小程序内使用 |

还可以选择对 SDK 进行一些全局设置，这些设置都是可选的。

```js
import defaults from 'mdclubSDK/es/defaults';

// 设置 api 地址，默认为 {当前域名}/api
defaults.apiPath = 'https://example.com/api';

// 若浏览器不支持 patch, put, delete 方法，则需要将该选项设为 true
defaults.methodOverride = true;

// 设置全局回调函数
defaults.beforeSend = () => {};
defaults.success = () => {};
defaults.error = () => {};
defaults.complete = () => {};
```

然后就可以调用 SDK 方法了

```js
import QuestionApi from 'mdclubSDK/es/QuestionApi';

QuestionApi.getList({ question_id: 11 })
.then((questions) => {
  console.log(questions);
})
.catch((errMsg) => {
  console.log(errMsg);
});
```
