# mdclub-admin

mdclub 的后台管理界面

## 项目说明

本项目使用 mdui 作为 UI 框架，使用 hyperapp 作为 SPA 框架。

## 相关项目

mdclub 后端源码：https://github.com/zdhxiong/mdclub

mdclub 前端 Material Design 主题源码：https://github.com/zdhxiong/mdclub-theme-material

## 安装说明

### 克隆项目源码到本地

```
$ git clone https://github.com/zdhxiong/mdclub-admin
```

### 安装项目依赖

```
$ cd mdclub-admin
$ npm install
```

### 运行开发环境

首先修改配置，打开 `index.html`，将其中的 `window.G_API` 修改为服务器的 API 地址。然后执行：

```
$ npm run dev
```

在浏览器中访问 `http://localhost:8080` 即可。

### 打包生产环境

```
$ npm run build
```

打包后的文件位于 `dist` 目录中，将该目录中的文件全部复制到 `mdclub` 的 `public/static/admin` 目录中即可。


