<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>深圳市名扬科技</title>
    @vite(['resources/js/frontend/main.js'])
</head>
<body>
    <div id="frontend-app"></div>
</body>
</html>
