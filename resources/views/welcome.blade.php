<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SaaS Tenancy App') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
    <div id="app" data-app-scope="{{ function_exists('tenant') && tenant() ? 'tenant' : 'central' }}"></div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
