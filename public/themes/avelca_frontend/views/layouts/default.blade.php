<!DOCTYPE html>
<html id="newhome" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Site Properities -->
    <title>Asia Foundation Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ Theme::asset('img/favicon.ico') }}">

    <!-- Loading Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/select2.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/dd.css') }}">

    <script src="{{ Theme::asset('javascript/jquery-1.7.2.min.js') }}"></script>
    <script src="{{ Theme::asset('javascript/modernizr-2.6.2.min.js') }}"></script>
  </head>

  <body>
  @yield('content')
  <section class="sponsorship">
    <div class="container">
      <div class="col-md-12">
        <img src="{{ Theme::asset('img/sponsor.png') }}" alt usemap="#logos">
        <map name="logos">
          <area shape="rect" coords="75, 2, 196, 117" href="http://wgs.co.id/" title="wgs" alt="wgs" target="_blank">
          <area shape="rect" coords="279, 3, 642, 117" href="http://asiafoundation.org/" title="asiafoundation" alt="asiafoundation" target="_blank">
          <area shape="rect" coords="696, 2, 895, 119" href="http://www.polling-center.com/" title="pollingcenter" alt="pollingcenter" target="_blank">
        </map>
      </div>
    </div>
  </section>
  <!-- <footer>
    <div class="container center">
      <div class="col-md-12">
        <a href="#"><img src="{{ Theme::asset('img/logo-footer.png') }}"></a>
        <p>Survey Q Copyright 2014. All rights reserved.</p>
      </div>
    </div>
  </footer>
 -->
  
</body>
</html>