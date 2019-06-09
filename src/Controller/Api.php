<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * API
 */
class Api extends ContainerAbstracts
{
    /**
     * API 接口页面
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function pageIndex(Request $request, Response $response): Response
    {
        return $response->write(<<<END
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MDClub API</title>
  <link rel="stylesheet" type="text/css" href="//fonts.loli.net/css?family=Open+Sans:400,700|Source+Code+Pro:300,600|Titillium+Web:400,600,700"/>
  <link rel="stylesheet" type="text/css" href="//cdnjs.loli.net/ajax/libs/swagger-ui/3.20.3/swagger-ui.css"/>
  <style>
    html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
    *, *:before, *:after { box-sizing: inherit; }
    body { margin:0; background: #fafafa; }
  </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="//cdnjs.loli.net/ajax/libs/swagger-ui/3.20.3/swagger-ui-bundle.js"> </script>
<script src="//cdnjs.loli.net/ajax/libs/swagger-ui/3.20.3/swagger-ui-standalone-preset.js"> </script>
<script>
  window.onload = function() {
    window.ui = SwaggerUIBundle({
      dom_id: "#swagger-ui",
      presets: [ SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset ],
      plugins: [ SwaggerUIBundle.plugins.DownloadUrl ],
      layout: "StandaloneLayout",
      url: "./openapi.yaml",
    });
  }
</script>
</body>
</html>
END
        );
    }
}
