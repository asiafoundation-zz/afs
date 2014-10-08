<!DOCTYPE html>
<html id="newhome" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Site Properities -->
    <title>{{ Setting::meta_data('general', 'name')->value }} - {{ Setting::meta_data('general', 'tag_line')->value }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/selectik.css') }}">

    <script src="{{ Theme::asset('javascript/jquery-1.7.2.min.js') }}"></script>
    <script src="{{ Theme::asset('javascript/modernizr-2.6.2.min.js') }}"></script>

    <!-- Map JS-->
    <script src="{{ Theme::asset('javascript/leaflet.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/leaflet.css') }}">
    <script type="text/javascript" src="{{ Theme::asset('geojson/geojson.geojson') }}"></script>
  </head>

  <body>

