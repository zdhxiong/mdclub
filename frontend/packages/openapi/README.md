# MDClub 的 OpenAPI 描述文件


### 生成 `openapi.yaml` 文件

`npm run build`

### 生成 SDK 和 SDK文档

1. 需要安装 [JRE](https://www.java.com/zh_CN/download/) 和 [Apache maven](http://maven.apache.org/download.cgi)，并添加到系统的环境变量

2. 下载 [swagger-codegen-cli](https://oss.sonatype.org/content/repositories/snapshots/io/swagger/codegen/v3/swagger-codegen-cli/3.0.9-SNAPSHOT/swagger-codegen-cli-3.0.9-20190628.091749-45.jar)，执行：

`java -jar swagger-codegen-cli.jar generate -i dist/openapi.yaml -l php -o ./dist/php`

也可以把 openapi.yaml 文件复制到 [https://swagger.io/editor](https://swagger.io/editor)，在线生成文档和 SDK。
