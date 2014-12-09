  <script type="text/javascript">
      $('.loading-flag').hide();
     function find_survey()
     {
      // Get cycles functions
      clear_all_filter_nosurvey();
      clear_text_notification();
      $('#chart_canvas').hide();
      $('.loading-flag').show();
      $.get( "filter-select", { SelectedFilter:"survey",region: FilterSelect.region,region_dapil: FilterSelect.region_dapil, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
        .done(function( data ) {
          $('.region-name').remove();
          $('#chart_canvas').show();
          $('.loading-flag').hide();
          if (data != false) {
            // Re declare object filter data 
            cycle_id = FilterSelect.cycle;

            $("#question-name").html(data.default_question.question);
            $("#select_cycle_label").html(cycle_text);
            $("#select_category_label").html(data.default_question.question_categories.slice(0,15)+" ...");
            $("#select_question_label").html(data.default_question.question.slice(0,40)+" ...");

             if(data.empty_answer == 1){
              $(".notification").html('<div class="alert alert-info"><h4>{{Lang::get('frontend.empty_data')}} <br>'+ data.default_question.question +'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
              $(".chart #chart_canvas").hide();
              return false;
            }

            FilterSelect.answers = [];
            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }
            // cycle list
            var data_cycles_length = 0;
            var cycle_list = "";
            var data_cycles = data.cycles;
            for (var key in data_cycles) {
              if (data_cycles.hasOwnProperty(key)) {
                cycle_list =cycle_list+'<li><a href="#" onclick="cycle_select('+data_cycles[key].id+')" id="'+data_cycles[key].id+'">'+data_cycles[key].name+'</a></li>';

                // Count Data
                data_cycles_length=data_cycles_length+1;
              }
            }

            // Build chart
            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);
            var data_points_pie_data = data_points_pie(data.question);

            $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
            chartjs(color_set_data,data_points_data,data_points_pie_data);

            var cycle_text = $("#cycle_select_"+cycle_id).text();
            $("#cycle_list").html(cycle_list);            

            // Is Has Compare Cycle
            var is_has_compare = data_cycles_length > 1 ? '<li id="chart_pagination_text"><a class="orange-bg" onclick="compare_cycle(0)">{{Lang::get('frontend.compare_this_survey')}}</a></li>' : '';
            var chart_pagination = '<li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get("frontend.preveous_question") }}</a></li>'+is_has_compare+'<li><a class="orange-bg" onclick="next_question(1)">{{ Lang::get("frontend.next_question") }} <img src="{{ Theme::asset('img/arrow.png') }}"></a></li>';

            $(".chart-pagination").html(chart_pagination);

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

            var last_question = $('#s2id_select-question').children().children().html();
            $('.loading-flag').hide();
            // $('#chart_canvas .col-md-7, #chart_canvas .col-md-5').hide();
            $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.empty_data')}}'+last_question+'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
      return false;
     }

     function cycle_select(cycle_id)
     {
        clear_text_notification();
        // Re declare object filter data 
        FilterSelect.cycle = cycle_id;

        $('#chart_canvas').hide();
        $('.loading-flag').show();
        // Get cycles functions
        $.get( "filter-select", {SelectedFilter:"cycle", cycle: cycle_id} )
          .done(function( data ) {
            if (data != false) {
              $('#chart_canvas').show();
              $('.loading-flag').hide();

              var cycle_text = $("#cycle_select_"+cycle_id).text();
              $("#select_cycle_label").html(cycle_text);

              FilterSelect.answers = [];
              for (var key in data.question) {
                if (data.question.hasOwnProperty(key)) {
                  FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
                }
              }

              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);
              var data_points_pie_data = data_points_pie(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data,data_points_pie_data);

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
              var last_question = $('#s2id_select-question').children().children().html();
              $('.loading-flag').hide();
              $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.empty_data')}}'+last_question+'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
        },"html");
     }

     function filter_option(value)
     {
        // clear_text_notification();
        var is_region = false;
        $('.notification').html("");
                
        text_area_filter_process = text_area_filter(value);

        if(value == 0){
          var option_filters = "";
          var filter_text = "";
        }else{
          var option_filters = text_area_filter_process[0];
          var filter_text = text_area_filter_process[1];
        }
        if(option_filters.length != 0){
        // Get cycles functions
          $('#chart_canvas').hide();
          $('.loading-flag').show();
          $.get( "filter-select", { SelectedFilter:"filters",region: FilterSelect.region,region_dapil: FilterSelect.region_dapil, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, option_filters: option_filters} )
          .done(function( data ) {
            $('.loading-flag').hide();
            $('#chart_canvas').show();
            if (data != false) {
              // Build chart
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);
              var data_points_pie_data = data_points_pie(data.question);

              $("#chart_canvas").html('<div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div><div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>');
              chartjs(color_set_data,data_points_data,data_points_pie_data);

              // Is Has Compare Cycle
              var data_cycles_length = 0;
              var data_cycles = data.cycles;
              for (var key in data_cycles) {
                if (data_cycles.hasOwnProperty(key)) {
                data_cycles_length=data_cycles_length+1;
                }
              }
              var is_has_compare = data_cycles_length > 1 ? '<li id="chart_pagination_text"><a class="orange-bg" onclick="compare_cycle(0)">{{Lang::get('frontend.compare_this_survey')}}</a></li>' : '';
              var chart_pagination = '<li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get("frontend.preveous_question") }}</a></li>'+is_has_compare+'<li><a class="orange-bg" onclick="next_question(1)">{{ Lang::get("frontend.next_question") }} <img src="{{ Theme::asset('img/arrow.png') }}"></a></li>';
              $(".chart-pagination").html(chart_pagination);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);

              // Show label
              $("#filter-by-label").text(filter_text);
            }else
            {
              var last_question = $('#s2id_select-question').children().children().html();
              $('.loading-flag').hide();
              $("#chart_canvas").hide();
              $('.chart chart-flag .chart-pagination').hide();
              $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.empty_filter_data')}} '+ filter_text +'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
              // Re assingn Filter `
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
        }else{
          find_survey();
        }
     }

    function compare_cycle(move)
    {
      clear_text_notification();
      $('#chart_canvas').hide();
      $('.loading-flag').show();
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"compare_cycle",region: FilterSelect.region,region_dapil: FilterSelect.region_dapil, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, FilterMove: move} )
        .done(function( data ) {
          if (data != false) {
            // $('html, body').animate({scrollTop: $(".survey-question").offset().top}, 1000);
            $('#chart_canvas').show();
            $('.loading-flag').hide();
            // Build chart
            $("#chart_canvas").html('<div class="col-md-12"><div id="compareChart" style="height: 345px; width: 100%;"></div></div>');

            var first_list = [];
            var end_list = [];
            var colorSet = [];
            var baseline_text = "";
            var endline_text = "";
            var question_text = "";

            FilterSelect.answers = [];
            for (i = 0; i < data.question.length; i++) {
              if (data.question[i].cycle_type == 0) {

                baseline_text = data.question[i].cycle;
                question_text = data.question[i].question;
                FilterSelect.question = data.question[i].id_question;

                first_list.push({ y: parseInt(data.question[i].amount), label: data.question[i].answer});

                colorSet.push(data.question[i].color);
                FilterSelect.answers.push({ id: data.question[i].id_answer, answer: data.question[i].answer});
              }
              if (data.question[i].cycle_type == 1) {
                endline_text = data.question[i].cycle;
                end_list.push({ y: parseInt(data.question[i].amount), label: data.question[i].answer});
              }
            }

            compare_chart(first_list,end_list, colorSet, baseline_text,endline_text);

            if (move == 0) {
              $('.chart-pagination').html('<li>&nbsp;</li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li>&nbsp;</li>');
            }else{
              $("#question-name").html(question_text);

              if (Object.keys(data.cycles).length > 1) {
                $(".chart-pagination").html('<li><a class="orange-bg" onclick="compare_cycle(1)"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get("frontend.preveous_question") }}</a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" onclick="compare_cycle(2)">{{ Lang::get("frontend.next_question") }} <img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');
              }
            }

            // Re assingn Filter data
            DefaultSelectAssign(FilterSelect);
          }else
          {
            var last_question = $('#s2id_select-question').children().children().html();
            $('.loading-flag').hide();
            $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.comparing_cycle_failed')}}</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
    }

    function next_question(move)
    {
      clear_all_filter_nosurvey();
      clear_text_notification();
      $('#chart_canvas').hide();
      $('.loading-flag').show();
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"next_question",region: FilterSelect.region,region_dapil: FilterSelect.region_dapil, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle,FilterMove:move, empty: FilterSelect.empty_question})
        .done(function( data ) {
          if (data != false) {
            $('.loading-flag').hide();

            /*-- compare availability --*/
            $('#chart_pagination_text a').show();
            if(data.compare_available == 0){
              $('#chart_pagination_text a').hide();
            }
            /*-- End --*/

            $('#select-question').val(data.default_question.id_question);
            $('.select-question .select2-chosen').text(data.default_question.question);

            $("#question-name").html(data.default_question.question);
            $("#select_category_label").html(data.default_question.question_categories.slice(0,10)+" ...");
            $("#select_question_label").html(data.default_question.question.slice(0,40)+" ...");

            FilterSelect.question = parseInt(data.default_question.id_question);
            if(data.empty_answer == 1){
              $(".notification").html('<div class="alert alert-info"><h4>{{Lang::get('frontend.empty_data')}} <br>'+ data.default_question.question +'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
              $(".chart #chart_canvas").hide();
              return false;
            }

            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }


            if(data.regions != 0){
              $('#chart_canvas').show();
              // Re assingn Filter data
              

              DefaultSelectAssign(FilterSelect);

              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);
              var data_points_pie_data = data_points_pie(data.question);
              chartjs(color_set_data,data_points_data,data_points_pie_data);

              // Re assign map
              dynamicRegions = data.regions;
              // Load New map
              geojson = L.geoJson(statesData, {
                style: styleDynamic,
                onEachFeature: onEachFeature,
              }).addTo(map);
            }
            else{

              var last_question = $('#s2id_select-question').children().children().html();
              $('.loading-flag').hide();
              $('#chart_canvas').hide();
              $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.empty_data')}}</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
              // Re assingn Filter data
              // DefaultSelectAssign(DefaultSelect);   
            }
          }else
          {
            var last_question = $('#s2id_select-question').children().children().html();
            $('.loading-flag').hide();
            $('#chart_canvas').hide();
            $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.empty_data')}}'+last_question+'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
    }

    function find_survey_dynamic(value)
    {
      // Show text information under graph
      text_area_filter_process = text_area_filter(value);

      var filter_text = text_area_filter_process[1];

      // clear_all_filter_nosurvey();
      $(".notification").html("");
      clear_text_notification();
      $('#chart_canvas').hide();
      $('.loading-flag').show();
      // Get cycles functions
      $.get( "filter-select", { SelectedFilter:"survey_area_dynamic",region: value,region_dapil: FilterSelect.region_dapil, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
        .done(function( data ) {
          if (data != false) {
            // $('html, body').animate({scrollTop: $(".survey-question").offset().top}, 1000);
            
            FilterSelect.question = parseInt(data.default_question.id_question);
            FilterSelect.category = parseInt(data.default_question.id_question_categories);
            FilterSelect.cycle = parseInt(data.default_question.id_cycle);

            $('#chart_canvas').show();
            $('.loading-flag').hide();

            // $("#question-name").html(data.default_question.question);
            $("#question-name").html(data.default_question.question);
            // if(FilterSelect.region != ""){
            //   $('.survey-question label span').remove();
            //   $('.survey-question label').append('<span> DI '+ data.default_question.region_name.toUpperCase() +'</span>');
            // }

            // Re assingn Filter data
            FilterSelect.question = data.default_question.id_question;
            FilterSelect.answers = [];
            for (var key in data.question) {
              if (data.question.hasOwnProperty(key)) {
                FilterSelect.answers.push({ id: data.question[key].id_answer, answer: data.question[key].answer});
              }
            }

            DefaultSelectAssign(FilterSelect);
            // Build chart
            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);
            var data_points_pie_data = data_points_pie(data.question);
            chartjs(color_set_data,data_points_data,data_points_pie_data);

            // Show label
            $("#filter-by-label").text(filter_text);
          }else
          {
            var last_question = $('#s2id_select-question').children().children().html();
              $('.loading-flag').hide();
              $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.empty_data')}}'+last_question+'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
            // Re assingn Filter data
            DefaultSelectAssign(DefaultSelect);
          }
        },"html");
     }

     function detail_chart(answer_id,category_id,move)
     {
      clear_text_notification();
      $('#chart_canvas').hide();
      $('.loading-flag').show();
        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"detail_chart",region: FilterSelect.region,region_dapil: FilterSelect.region_dapil, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answer_id:answer_id, category_filter: category_id, FilterMove:move} )
          .done(function( data ) {
            if (data != false) {
              $('#chart_canvas').show();
              $('.loading-flag').hide();

              $("#chart_canvas").html('<div class="col-md-12"><div id="detailChart" style="margin-top: 5px; height: 345px; width: 100%;"></div></div>');
              detail_chart_js(data.question);

              // Re assingn Filter data
              DefaultSelectAssign(FilterSelect);
              $('.chart-pagination').html('<li><a class="orange-bg" onclick="detail_chart('+answer_id+','+data.default_question.id_category+',1)"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get("frontend.preveous_question") }}</a></li><li id="chart_pagination_text"><a class="orange-bg" onclick="find_survey()">{{Lang::get('frontend.return')}}</a></li><li><a class="orange-bg" onclick="detail_chart('+answer_id+','+data.default_question.id_category+',2)">{{ Lang::get("frontend.next_question") }} <img src="{{ Theme::asset('img/arrow.png') }}"></a></li>');
            }else
            {
              var last_question = $('#s2id_select-question').children().children().html();
              $('.loading-flag').hide();
              $(".notification").html('<div class="alert alert-info"><button class="close" type="button" data-dismiss="alert">×</button><h4>{{Lang::get('frontend.detail_chart_failed')}}'+last_question+'</h4></div><div id="chart_canvas"></div><div class="col-md-12"><ul class="chart-pagination"></div>');
              // Re assingn Filter data
              DefaultSelectAssign(DefaultSelect);
            }
          },"html");
    }

    function filter_option_regions(region_id,region_text)
    {
      $(".title-filters").each(function(){
          if ($(this).attr("data-type") === 'region') {
            FilterSelect.region = region_id;
          }else{
            var title = $(this).attr("data-title");
            $('#custom-text-title-'+title).text(title);
          }
        });

      find_survey();
    }

    /*
    //Default color
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
      var data_points = [];
      for (i = 0; i < color_set.length; i++) {
        if (color_set[i].y != 0) {
          data_points.push(color_set[i]);    
        }
      }
      return data_points;
    }*/

    function data_points(assign_answer)
    {
      if (assign_answer != null) 
      {
        var data_list = [];
        for (var key in assign_answer) {
          if (assign_answer.hasOwnProperty(key)) {
            data_list.push(
              { label: assign_answer[key]['answer'], answer_id: assign_answer[key]['id_answer'], y: assign_answer[key]['indexlabel'] }
              );
          }
        }
      }
      else
      {
        var data_list = [//colorSet Array
          @foreach ($question as $key => $answer)
            { label: "{{ $answer->answer }}", answer_id: "{{ $answer->id_answer }}",y: {{ $answer->indexlabel }} },
          @endforeach
          ];
      }

        var data_points = [];
        for (i = 0; i < data_list.length; i++) {
          if (data_list[i].y != 0) {
            data_points.push(data_list[i]);    
          }
        }
      return data_points;
    }

    function color_set(assign_answer)
    {
      if (assign_answer != null) 
      {
        var data_list = [];
        for (var key in assign_answer) {
          if (assign_answer.hasOwnProperty(key)) {
            data_list.push(
              { color: assign_answer[key]['color'], answer_id: assign_answer[key]['id_answer'], y: assign_answer[key]['indexlabel'] }
              );
          }
        }
      }
      else
      {
        var data_list = [//colorSet Array
          @foreach ($question as $key => $answer)
            { color: "{{ $answer->color }}", answer_id: "{{ $answer->id_answer }}",y: {{ $answer->indexlabel }} },
          @endforeach
          ];
      }

        var data_points = [];
        for (i = 0; i < data_list.length; i++) {
          if (data_list[i].y != 0) {
            data_points.push(data_list[i].color);    
          }
        }
      return data_points;
    }

    function data_points_pie(assign_answer)
    {
      if (assign_answer != null) 
      {
        var data_list = [];
        for (var key in assign_answer) {
          if (assign_answer.hasOwnProperty(key)) {
            data_list.push(
              { y: parseInt(assign_answer[key]['amount']), label: assign_answer[key]['answer'], answer_id: assign_answer[key]['id_answer']}
              );
          }
        }
      }
      else
      {
        var data_list = [//colorSet Array
          @foreach ($question as $answer)
              { y: {{ $answer->amount }}, label: "{{ $answer->answer }}", answer_id: "{{ $answer->id_answer }}"},
          @endforeach                  
          ];
      }
      var data_points = [];
        for (i = 0; i < data_list.length; i++) {
          if (data_list[i].y != 0) {
            data_points.push(data_list[i]);    
          }
        }
      return data_points;
    }
    function clear_text_notification(){
      // Remove Filter Text
      $("#filter-by-label").text("");
      // Remove Notification
      $(".notification").html("&nbsp;");
    }

    function text_area_filter(value){
      var option_filters = [];
      
      if(value != 0){
        if(option_filters_default.length != 0){
            for(i = 0; i < option_filters_default.length; i++) {
              if (value.toString() === option_filters_default[i].toString()) {
                return false;
              };
            }
          }

          var filter_text_type = "";
          option_filters_default = [];
          $(".dropdown-filter .selected_filter_option").each(function(){
            if ($(this).attr("data-type") === 'region') {
              // Set Default Value for option filters
              option_filters_default.push($(this).text());
              // Filter Text
              filter_text_type = filter_text_type+$('.title-filters',$(this).parent('ul')).text()+" "+$(this).text()+","
              FilterSelect.region = $(this).attr("data-value") == 0 ? FilterSelect.region : $(this).attr("data-value");
            }else{
              var data_value = $(this).attr("data-value");
              if(data_value % 1 === 0){
                // Filter Text
                filter_text_type = filter_text_type+$('.title-filters',$(this).parent('ul')).text()+" "+$(this).text()+","
                option_filters += $(this).attr("data-value")+",";

                // Set Default Value for option filters
                option_filters_default.push($(this).attr("data-value"));
              }
              else{
                // Set Default Value for option filters
                option_filters_default.push($(this).text());
              }
            }
          });
          filter_text = "{{Lang::get('frontend.show_responnden_filter_result')}}"+filter_text_type;
          filter_text = filter_text.substring(0, filter_text.length - 1);
        }else{
          option_filters_default.length = 0;
          option_filters = [];
          filter_text = "";
        }

        return [option_filters, filter_text];
    }
</script>