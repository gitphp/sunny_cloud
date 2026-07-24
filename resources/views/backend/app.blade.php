<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>名杨后台管理系统</title>
    @vite(['resources/js/backend/main.js'])
</head>
<body>
    <div id="backend-app"></div>
</body>
</html>
