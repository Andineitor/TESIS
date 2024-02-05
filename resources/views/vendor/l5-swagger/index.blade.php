<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>L5 Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://cargod-cb38f5c42af3.herokuapp.com/public/docs/asset/swagger-ui.css?v=3fb53292764c7c9a0ee0928832bfbe54">
    <link rel="icon" type="image/png" href="https://cargod-cb38f5c42af3.herokuapp.com/public/docs/asset/favicon-32x32.png?v=40d4f2c38d1cd854ad463f16373cbcb6" sizes="32x32"/>
    <link rel="icon" type="image/png" href="https://cargod-cb38f5c42af3.herokuapp.com/public/docs/asset/favicon-16x16.png?v=f0ae831196d55d8f4115b6c5e8ec5384" sizes="16x16"/>
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
    </style>
</head>

<body>
<div id="swagger-ui"></div>

<script src="https://cargod-cb38f5c42af3.herokuapp.com/public/docs/asset/swagger-ui-bundle.js?v=6456bfd9afa0d3914d7eaa5a80506d20"></script>
<script src="https://cargod-cb38f5c42af3.herokuapp.com/public/docs/asset/swagger-ui-standalone-preset.js?v=f2e8d34c39f7b7a59647d27eedbb5a46"></script>
<script>
    window.onload = function() {
        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            url: "https://cargod-cb38f5c42af3.herokuapp.com/public/docs/openapi.json",
            operationsSorter: null,
            configUrl: null,
            validatorUrl: null,
            oauth2RedirectUrl: "https://cargod-cb38f5c42af3.herokuapp.com/public/api/oauth2-callback",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "none",
            deepLinking: true,
            filter: true,
            persistAuthorization: "false",
        })

        window.ui = ui
    }
</script>
</body>
</html>
