  <script type="text/javascript">
    window.onload = function () {
      var color_set_data = color_set(null);
      var data_points_data = data_points(null);

      chartjs(color_set_data,data_points_data);
    }

     function find_survey()
     {
        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"survey",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( data ) {
            if (data != false) {
              var color_set_data = color_set(data.question);
              var data_points_data = data_points(data.question);

              chartjs(color_set_data,data_points_data);
              $("#question-name").html(data.default_question.question);
            }else
            {
              alert("{{Lang::get('frontend.empty_data')}}");
            }
          },"html");
     }

     function cycle_select(cycle_id)
     {
        // Re declare object filter data 
        FilterSelect.cycle = cycle_id;
        FilterSelect.question = $("#question-name").text();

        // Get cycles functions
        $.get( "home", {SelectedFilter:"survey",category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( data ) {

            var color_set_data = color_set(data.question);
            var data_points_data = data_points(data.question);

            chartjs(color_set_data,data_points_data);
            $("#question-name").html(data.default_question.question);

            var cycle_text = $("#cycle_select_"+cycle_id).text();
            $("#select_cycle_label").html(cycle_text);
          },"html");
     }

    function chartjs(color_set,data_points)
    {
        // PIE CHART
        CanvasJS.addColorSet("greenShades",color_set);

        var chart = new CanvasJS.Chart("chartContainerPie",
        {
          colorSet: "greenShades",
          
          legend: {
            verticalAlign: "bottom",
            horizontalAlign: "center"
          },
          theme: "theme1",
          data: [
          {        
            type: "doughnut",
            indexLabelFontFamily: "DINNextLTPro-Regular",       
            indexLabelFontSize: 0,
            startAngle:0,
            indexLabel: "{label} #percent%",
            indexLabelFontColor: "#ffffff",       
            indexLabelPlacement: "inside", 
            toolTipContent: "{label}: {y} - <strong>#percent%</strong>",
            showInLegend: false,
            indexLabel: "#percent%", 
            dataPoints: data_points
          }
          ]
        });
        chart.render();

        // BAR CHART
        CanvasJS.addColorSet("greenShades",color_set);

        var chartbar = new CanvasJS.Chart("chartContainer", {

            colorSet: "greenShades",
            axisY: {
                tickThickness: 0,
                lineThickness: 0,
                valueFormatString: " ",
                gridThickness: 0                   
            },
            axisX: {
                tickThickness: 0,
                lineThickness: 0,
                labelFontSize: 18,
                labelFontColor: "gray"

            },
            data: [
            {
                indexLabelFontSize: 24,
                labelFontFamily: "DINNextLTPro-Regular",
                labelFontColor: "gray",
                labelFontSize: 18,
                indexLabelFontColor: "gray",
                indexLabelFontFamily: "DINNextLTPro-Regular",
                type: "bar",
                click: function(e){
                  e.dataPoint.answer_id
                 },
                dataPoints: data_points
                // dataPoints: [
                //     // { y: 2, label: "Tidak percaya pemilu", indexLabel: "2%" },
                //     // { y: 42, label: "Malas", indexLabel: "42%" },
                //     // { y: 18, label: "Bingung dengan pilihan", indexLabel: "18%" },
                //     // { y: 4, label: "Tidak tahu adanya pemilu", indexLabel: "4%" },                    
                //     // { y: 34, label: "Berhalangan", indexLabel: "34%" }
                //   @foreach ($question as $answer)
                //     { y: {{ $answer->amount }}, label: "{{ $answer->answer }}"},
                //   @endforeach   
                // ]
            }
            ]
        });

        chartbar.render();
        chartbar.onclick = function(evt){
            var activeBars = chartbar.getBarsAtEvent(evt);
            console.log(activeBars);
        };
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
              { y: parseInt(assign_answer[key]['amount']), label: assign_answer[key]['answer'], answer_id: assign_answer[key]['id_answer']}
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