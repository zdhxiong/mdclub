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

### 修改配置

打开 `build/config.js` 文件，将 `targetFolder` 修改为 `mdclub` 项目的 `public/static/admin` 目录的绝对路径。

### 运行

要运行开发环境，执行：

```
$ npm run dev
```

要打包生产环境，执行：

```
$ npm run build
```
