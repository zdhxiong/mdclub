# MDClub 的 Javascript 版 SDK

## 通过 CDN 引入 JS 文件

注意：这种方式只适用于浏览器环境。

在 HTML 中通过 `<script>` 标签引入 JS 文件：

```html
<script
  src="https://cdn.jsdelivr.net/npm/mdclub-sdk-js@1.0.4/dist/mdclub-sdk.min.js"
  integrity="sha256-h4XtKOU7LD8xH509/AbdZxu/4LiRvtxAUaRFCjj6xOk="
  crossorigin="anonymous"
></script>
```

然后就可以通过全局变量 `mdclubSDK` 访问 SDK 了。

你可以先通过 `mdclubSDK.defaults` 对 SDK 进行一些设置：

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
```

然后可以如下调用 SDK 中的方法：

```js
mdclubSDK.QuestionApi.getList({ question_id: 11 })
.then((questions) => {
  console.log(questions);
})
.catch((errMsg) => {
  console.log(errMsg);
});
```

## 在 ES6 模块化环境或小程序中使用

使用 `npm` 安装 SDK：

```bash
npm install mdclub-sdk-js --save
```

SDK 同时支持浏览器环境和小程序环境，你需要根据使用环境设置适配器。

若在小程序中使用，需要设置成小程序适配器。支持微信小程序、支付宝小程序、钉钉小程序、百度小程序（注意：当前代码还未在小程序中进行过测试）：

```js
import defaults from 'mdclub-sdk-js/es/defaults';
import MiniProgramAdapter from 'mdclub-sdk-js/es/adapter/MiniProgramAdapter';

defaults.adapter = new MiniProgramAdapter();
```

若在浏览器中使用，设置成浏览器适配器：

```js
import defaults from 'mdclub-sdk-js/es/defaults';
import BrowserAdapter from 'mdclub-sdk-js/es/adapter/BrowserAdapter';

defaults.adapter = new BrowserAdapter();
```

若你的项目中已经通过 npm 安装了 jQuery、axios 之类的库，则可以设置对应的适配器，SDK 将直接使用对应的库提供的 ajax 方法。下面是所有支持的适配器：

| 适配器 | 使用方法 | 说明 |
| ---- | ---- | ---- |
| `AxiosAdapter` | `import defaults from 'mdclub-sdk-js/es/defaults';`<br/>`import AxiosAdapter from 'mdclub-sdk-js/es/adapter/AxiosAdapter';`<br/>`defaults.adapter = new AxiosAdapter();` | 使用 [axios](https://github.com/axios/axios) 发送请求 |
| `BrowserAdapter` | `import defaults from 'mdclub-sdk-js/es/defaults';`<br/>`import BrowserAdapter from 'mdclub-sdk-js/es/adapter/BrowserAdapter';`<br/>`defaults.adapter = new BrowserAdapter();` | 使用 [mdui.jq](https://github.com/zdhxiong/mdui.jq) 发送请求 |
| `FetchAdapter` | `import defaults from 'mdclub-sdk-js/es/defaults';`<br/>`import FetchAdapter from 'mdclub-sdk-js/es/adapter/FetchAdapter';`<br/>`defaults.adapter = new FetchAdapter();` | 使用浏览器内置的 `fetch` 方法发送请求 |
| `JQueryAdapter` | `import defaults from 'mdclub-sdk-js/es/defaults';`<br/>`import JQueryAdapter from 'mdclub-sdk-js/es/adapter/JQueryAdapter';`<br/>`defaults.adapter = new JQueryAdapter();` | 使用 [jQuery](https://github.com/jquery/jquery) 发送请求 |
| `MiniProgramAdapter` | `import defaults from 'mdclub-sdk-js/es/defaults';`<br/>`import MiniProgramAdapter from 'mdclub-sdk-js/es/adapter/MiniProgramAdapter';`<br/>`defaults.adapter = new MiniProgramAdapter();` | 在小程序内使用 |

还可以选择对 SDK 进行一些全局设置，这些设置都是可选的。

```js
import defaults from 'mdclub-sdk-js/es/defaults';

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
import QuestionApi from 'mdclub-sdk-js/es/QuestionApi';

QuestionApi.getList({ question_id: 11 })
.then((questions) => {
  console.log(questions);
})
.catch((errMsg) => {
  console.log(errMsg);
});
```

## API 分类

SDK 中的方法按 API 的功能分类到了不同模块中，具体如下表所列：

| API 模块 | 说明 |
| ---- | ---- |
| `AnswerApi` | 回答相关 API |
| `ArticleApi` | 文章相关 API |
| `CaptchaApi` | 验证码相关 API |
| `CommentApi` | 评论相关 API |
| `EmailApi` | 邮件相关 API |
| `ImageApi` | 图片相关 API |
| `NotificationApi` | 通知相关 API |
| `OptionApi` | 配置相关 API |
| `QuestionApi` | 提问相关 API |
| `ReportApi` | 举报相关 API |
| `StatsApi` | 数据统计相关 API |
| `TokenApi` | 身份验证相关 API |
| `TopicApi` | 话题相关 API |
| `UserApi` | 用户相关 API |

各个模块中所有的方法详细说明，请参见 [SDK 文档](http://mdclub.site/sdk-js/) 。

若通过 CDN 引入 SDK，可以这样访问指定模块的方法：

```js
mdclubSDK.QuestionApi.getList().then((response) => {});
```

若在 ES6 模块化环境中使用 SDK，可以这样访问指定模块的方法：

```js
import QuestionApi from 'mdclub-sdk-js/es/QuestionApi';

QuestionApi.getList().then((response) => {});
```

## 错误代码

在 SDK 中，每一个错误代码都赋值给了一个常量。在开发中，你可以使用常量来代替错误代码，以便使你的代码拥有更好的可读性。

若通过 CDN 引入 SDK，可以这样访问错误代码常量：

```js
// 以下对应错误代码 201001
mdclubSDK.errors.USER_NEED_LOGIN;
```

若在 ES6 模块化环境中使用 SDK，可以这样访问错误代码常量：

```js
import errors from 'mdclub-sdk-js/es/errors';

// 以下对应错误代码 201001
errors.USER_NEED_LOGIN;
```

具体的常量和错误代码对应关系请 [参见源码](https://github.com/zdhxiong/mdclub-sdk-js/blob/master/src/errors.ts) 。
