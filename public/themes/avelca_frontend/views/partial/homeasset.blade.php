    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/selectik.css') }}">
    <script type="text/javascript" src="{{ Theme::asset('javascript/jquery.selectik.js') }}"></script>

    <!-- Map JS-->
    <script src="{{ Theme::asset('javascript/leaflet.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/leaflet.css') }}">
    <script type="text/javascript" src="{{ Theme::asset('geojson/geojson.geojson') }}"></script>

  <script type="text/javascript">
    window.onload = function () {
      // Load Filter plugin
      $('.select-control').selectik({
        width: 150,
      });

      // Load Chart Plugin
      var color_set_data = color_set(null);
      var data_points_data = data_points(null);
      var data_points_pie_data = data_points_pie(null);

      chartjs(color_set_data,data_points_data,data_points_pie_data);
    }
  </script>