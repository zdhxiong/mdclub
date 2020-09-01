## [MDClub 官网](https://mdclub.org) | [在线体验](https://community.mdclub.org)

## 项目说明

这是 MDClub 的前端代码。使用 [MDUI](https://mdui.org) 作为 UI 框架，[hyperapp](https://github.com/jorgebucaran/hyperapp) 作为 SPA 框架。

## 相关项目

MDClub 由以下子项目组成：

| 项目  | 说明 |
| ---- | ---- |
| [mdclub](https://github.com/zdhxiong/mdclub) | MDClub 服务端代码 |
| [mdclub-theme-material](https://github.com/zdhxiong/mdclub-theme-material) | MDClub 前端代码 |
| [mdclub-admin](https://github.com/zdhxiong/mdclub-admin) | MDClub 后台管理系统代码 |
| [mdclub-openapi](https://github.com/zdhxiong/mdclub-openapi) | MDClub 的 OpenApi 描述文件代码 |
| [mdclub-sdk-js](https://github.com/zdhxiong/mdclub-sdk-js) | MDClub 的 JavaScript SDK 代码 |

## 在此项目基础上进行二次开发

你需要先在本地安装 [mdclub](https://github.com/zdhxiong/mdclub) 和 [mdclub-theme-material](https://github.com/zdhxiong/mdclub-theme-material) 两个项目，且两个项目需要位于同一文件夹内。

mdclub 项目的安装过程详见 https://github.com/zdhxiong/mdclub 。

### mdclub-theme-material 的安装步骤

**克隆项目源码到本地**

```
git clone https://github.com/zdhxiong/mdclub-theme-material
```

**安装项目依赖**

```
$ cd mdclub-theme-material
$ npm install
```

**运行开发环境**

```
npm run dev
```

**打包生产环境**

```
npm run build
```

打包后的资源中，PHP 模板文件将位于 mdclub 项目的 /templates/material 目录中，静态资源文件将位于 mdclub 项目的 /public/static/theme/material 目录中。
