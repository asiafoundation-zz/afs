    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/selectik.css') }}">
    <script type="text/javascript" src="{{ Theme::asset('javascript/jquery.selectik.js') }}"></script>

    <!-- Map JS-->
    <script src="{{ Theme::asset('javascript/leaflet.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/leaflet.css') }}">
    <script type="text/javascript" src="{{ Theme::asset('geojson/geojson.geojson') }}"></script>

  <script type="text/javascript">
    $(document).ready(function () {
      $('.select-control').selectik({
        width: 200,
      });
    });
  </script>