<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MDClub API</title>
    <link rel="stylesheet" type="text/css" href="https://fonts.loli.net/css?family=Open+Sans:400,700|Source+Code+Pro:300,600|Titillium+Web:400,600,700"/>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3.31.1/swagger-ui.css"/>
    <style>
        html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin:0; background: #fafafa; }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@3.31.1/swagger-ui-bundle.js"> </script>
<script src="https://unpkg.com/swagger-ui-dist@3.31.1/swagger-ui-standalone-preset.js"> </script>
<script>
    window.onload = function() {
        window.ui = SwaggerUIBundle({
            dom_id: "#swagger-ui",
            presets: [ SwaggerUIBundle.presets.apis, SwaggerUIStandalonePreset ],
            plugins: [ SwaggerUIBundle.plugins.DownloadUrl ],
            layout: "StandaloneLayout",
            url: "./static/openapi.yaml",
        });
    }
</script>
</body>
</html>
