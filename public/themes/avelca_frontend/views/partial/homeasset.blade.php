    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/selectik.css') }}">

    <script type="text/javascript" src="{{ Theme::asset('javascript/jquery.selectik.js') }}"></script>

    <!-- Map JS-->
    <script src="{{ Theme::asset('javascript/leaflet.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/leaflet.css') }}">
    <script type="text/javascript" src="{{ URL::to('/uploads') }}/{{ $survey->geojson_file}}"></script>

  <script type="text/javascript">
    window.onload = function () {
      // Load Filter plugin
      $('.select-control').selectik({
        width: 200,
      });

      // Load Chart Plugin
      var color_set_data = color_set(null);
      var data_points_data = data_points(null);
      var data_points_pie_data = data_points_pie(null);

      chartjs(color_set_data,data_points_data,data_points_pie_data);

      $('.li-region li.title-filters').removeAttr('onclick');
      $('.li-region .title-filters').data('value', '0');

      $(".dropdown-filter .selected_filter_option").each(function(){
          if ($(this).attr("data-type") === 'region') {
            FilterSelect.region = $(this).attr("data-value") == 0 ? FilterSelect.region : $(this).attr("data-value");
            // Set Default Value for option filters
            option_filters_default.push($(this).attr("data-value"));
          }else{
            var data_value = $(this).attr("data-value");
            if(data_value % 1 === 0){
              // Filter Text
              filter_text_type = filter_text_type+$('.title-filters',$(this).parent('ul')).text()+" "+$(this).text()+","
              option_filters += $(this).attr("data-value")+",";

              // Set Default Value for option filters
              option_filters_default.push($(this).attr("data-value"));
            }else{
              // Set Default Value for option filters
              option_filters_default.push($(this).text());
            }
          }
        });
      // $('#select-question').prepend("<option value='0'>Pilih pertanyaan</option>");
      // $('#select-category').prepend("<option value='0'>Pilih category</option>");
      // $('#select-cycle').prepend("<option value='0'>Pilih jenis survey</option>");
      // $('html, body').animate({scrollTop: $(".filter").offset().top + 100}, 1000);
    }
  </script>