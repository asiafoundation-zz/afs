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