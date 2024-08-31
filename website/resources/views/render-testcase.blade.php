<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <script>
        // There are probably problems with floating-point precision when passing data
        // from PHP -> JSON -> JS. This seems to work for now.
        window.testCase = @js($case)
    </script>

    @vite(["resources/css/app.css"])
    @vite(["resources/js/tests/helpers/e2e.ts"])

    <style>
        #cartesian-flip {
            transform: scaleY(-1) !important;
        }
    </style>
</head>
<body class="h-full">
    <svg width="100%" height="100%" id="map">
        <g id="zoom-wrapper">
            <g id="center-wrapper">
                <g id="cartesian-flip">

                </g>
            </g>
        </g>
    </svg>
</body>
</html>
