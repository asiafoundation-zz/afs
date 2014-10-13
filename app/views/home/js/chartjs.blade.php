  <script type="text/javascript">
    window.onload = function () {
      chartjs();
    }

     function find_survey()
     {
        // Get cycles functions
        $.get( "filter-select", { SelectedFilter:"survey",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( data ) {
            console.log(data);
            $(".survey-pemilu").html(data);
            chartjs();
          },"html");
     }

     function cycle_select(cycle_id)
     {
        // Re declare object filter data 
        FilterSelect.cycle = cycle_id;

        // Get cycles functions
        $.get( "home", {SelectedFilter:"survey",category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( response ) {
            var cycle_text = $("#cycle_select_"+cycle_id).text();
            $("#cycle_select_"+cycle_id).html(cycle_text);
            $(".survey-pemilu").html(response);
            chartjs();
          },"html");
     }

    function chartjs()
    {
        // PIE CHART
        CanvasJS.addColorSet("greenShades",color_set(null));

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
            dataPoints: data_points(null)
          }
          ]
        });
        chart.render();


        // BAR CHART
        CanvasJS.addColorSet("greenShades",color_set(null));

        var chart = new CanvasJS.Chart("chartContainer", {

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
                dataPoints: data_points(null)
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

        chart.render();
    }
    function color_set(assign_color)
    {
      return [//colorSet Array
        @foreach ($question as $answer)
          "{{ $answer->color }}",
        @endforeach                 
        ];
      // var color_set = assign_color != null ? "aa":[//colorSet Array
      //   @foreach ($question as $answer)
      //     "{{ $answer->color }}",
      //   @endforeach                 
      //   ];
      //   alert(color_set);
      // return color_set;
    }
    function data_points()
    {
      return [
        @foreach ($question as $answer)
          { y: {{ $answer->amount }}, label: "{{ $answer->answer }}"},
        @endforeach   
      ];
    }
</script>