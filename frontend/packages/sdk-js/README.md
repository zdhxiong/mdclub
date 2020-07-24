# mdclub 的 Javascript 版 SDK

## 使用方法

### 通过 `<script>` 引入 JS 文件

这种方式只适用于浏览器环境。

```js
// 设置 api 地址（可选，默认为 {当前域名}/api）
mdclubSDK.defaults.apiPath = 'https://example.com/api';

// 若浏览器不支持 patch, put, delete 方法，则该选项设为 true
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


若在小程序中使用，设置成小程序适配器。支持微信小程序、支付宝小程序、钉钉小程序、百度小程序。

```js
import MiniProgram from 'mdclubSDK/es/adapter/MiniProgram';
defaults.adapter = new MiniProgram();
```

若在浏览器中使用，设置成浏览器适配器。

```js
import Browser from 'mdclubSDK/es/adapter/Browser';
defaults.adapter = new Browser();
```

还可以选择对 SDK 进行一些全局设置，这些设置都是可选的。

```js
import defaults from 'mdclubSDK/es/defaults';

// 设置 api 地址，默认为 {当前域名}/api
defaults.apiPath = 'https://example.com/api';

// 若浏览器不支持 patch, put, delete 方法，则该选项设为 true
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
