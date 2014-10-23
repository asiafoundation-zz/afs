  <script type="text/javascript">
     function find_survey()
     {
        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"survey",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( data ) {
            if (data != false) {
              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data);

              // Re declare object filter data 
              cycle_id = FilterSelect.cycle;
              var cycle_text = $("#cycle_select_"+cycle_id).text();
              $("#select_cycle_label").html(cycle_text);
              $(".chart-pagination").html('<li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="compare_cycle()">{{Lang::get('frontend.compare_this_survey')}}</a></li><li><a class="orange-bg" onclick="next_question(1)"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');

              // Re assign map
              dynamicRegions = data.regions;
              // Load New map
              geojson = L.geoJson(statesData, {
                style: styleDynamic,
                onEachFeature: onEachFeature,
              }).addTo(map);
              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
     }

     function cycle_select(cycle_id)
     {
        // Re declare object filter data 
        FilterSelect.cycle = cycle_id;

        // Get cycles functions
        $.get( "filter-select", {SelectedFilter:"cycle",category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( data ) {
            if (data != false) {

              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data);

              var cycle_text = $("#cycle_select_"+cycle_id).text();
              $("#select_cycle_label").html(cycle_text);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
        },"html");
     }

     function filter_option(category_id)
     {
        var option_filters = [];
        $(".selected_filter_option").each(function(){
          var data_value = $(this).attr("data-value");

          if(data_value % 1 === 0){
            option_filters += $(this).attr("data-value")+",";
          }
        });

        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"filters",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, option_filters: option_filters} )
          .done(function( data ) {
            if (data != false) {
              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
     }

    function compare_cycle(category_id)
    {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"compare_cycle",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answers:FilterSelect.answers} )
        .done(function( data ) {
          if (data != false) {

            // Build chart
            $("#chart_canvas").html('<div class="col-md-12"><div id="compareChart" style="height: 345px; width: 100%;"></div></div>');
            compare_chart(data.question);

            $('.chart-pagination').html('<li><a class="orange-bg"><img src="{{ Theme::asset('img/footer-bg.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" ><img src="{{ Theme::asset('img/footer-bg.png') }}"></a></li>');

            // Re assingn Filter data
            DefaultSelectAssign(FilterSelect);

          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
    }


    function next_question(move)
    {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"next_question",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle,FilterMove:move} )
        .done(function( data ) {
          if (data != false) {
            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);

            chartjs(color_set_data,data_points_data);
            $("#question-name").html(data.default_question.question);
            $("#select_category_label").html(data.default_question.question_categories);
            $("#select_question_label").html(data.default_question.question);

            // Re assingn Filter data
            FilterSelect.question = data.default_question.id_question;
            DefaultSelectAssign(FilterSelect);

            // Re assign map
            dynamicRegions = data.regions;
            // Load New map
            geojson = L.geoJson(statesData, {
              style: styleDynamic,
              onEachFeature: onEachFeature,
            }).addTo(map);
          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
    }
    function find_survey_dynamic()
    {
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"survey_area_dynamic",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
        .done(function( data ) {
          if (data != false) {
            // Build chart
            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);
            chartjs(color_set_data,data_points_data);

            // Re assingn Filter data
            DefaultSelectAssign(FilterSelect);
          }else
          {
            alert("{{Lang::get('frontend.empty_data')}}");
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
     }

     function detail_chart(answer_id,category_id,move)
     {
        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"detail_chart",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answer_id:answer_id, category_filter: category_id, FilterMove:move} )
          .done(function( data ) {
            if (data != false) {
              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);

              $("#chart_canvas").html('<div class="col-md-12"><div id="detailChart" style="height: 345px; width: 100%;"></div></div>');
              detail_chart_js(data.question);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
              $('.chart-pagination').html('<li><a class="orange-bg" onclick="detail_chart('+answer_id+','+data.default_question.id_category+',1)"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" onclick="detail_chart('+answer_id+','+data.default_question.id_category+',2)"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
     }


    function color_set(assign_color)
    {
      if (assign_color != null) 
      {
        var color_set = [];
        for (var key in assign_color) {
          if (assign_color.hasOwnProperty(key)) {
            color_set.push(assign_color[key]['color']);
          }
        }
      }
      else
      {
        var color_set = [//colorSet Array
          @foreach ($question as $answer)
            "{{ $answer->color }}",
          @endforeach                 
          ];
      }

      return color_set;
    }
    function data_points(assign_answer)
    {
      if (assign_answer != null) 
      {
        var data_points = [];
        for (var key in assign_answer) {
          if (assign_answer.hasOwnProperty(key)) {
            data_points.push(
              { y: parseInt(assign_answer[key]['amount']), label: assign_answer[key]['answer'], answer_id: assign_answer[key]['id_answer'],indexLabel:assign_answer[key]['indexlabel']+"%"}
              );
          }
        }
      }
      else
      {
        var data_points = [//colorSet Array
          @foreach ($question as $answer)
            { y: {{ $answer->amount }}, label: "{{ $answer->answer }}", answer_id: "{{ $answer->id_answer }}"},
          @endforeach                  
          ];
      }

      return data_points;
    }
</script>