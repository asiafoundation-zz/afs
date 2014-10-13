  <script type="text/javascript">
    window.onload = function () {

        // PIE CHART

        CanvasJS.addColorSet("greenShades",
                [//colorSet Array
                @foreach ($question as $answer)
                  "{{ $answer->color }}",
                @endforeach           
                ]);

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
            dataPoints: [
              // { y: 2, label: "Tidak percaya pemilu"},
              // { y: 42, label: "Malas"},
              // { y: 18, label: "Bingung dengan pilihan"},
              // { y: 4, label: "Tidak tahu adanya pemilu"},
              // { y: 34, label: "Berhalangan" }

              @foreach ($question as $answer)
                { y: {{ $answer->amount }}, label: "{{ $answer->answer }}"},
              @endforeach   
            ]
          }
          ]
        });
        chart.render();


        // BAR CHART

        CanvasJS.addColorSet("greenShades",
                [//colorSet Array
                @foreach ($question as $answer)
                  "{{ $answer->color }}",
                @endforeach                 
                ]);

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
                dataPoints: [
                    // { y: 2, label: "Tidak percaya pemilu", indexLabel: "2%" },
                    // { y: 42, label: "Malas", indexLabel: "42%" },
                    // { y: 18, label: "Bingung dengan pilihan", indexLabel: "18%" },
                    // { y: 4, label: "Tidak tahu adanya pemilu", indexLabel: "4%" },                    
                    // { y: 34, label: "Berhalangan", indexLabel: "34%" }
                  @foreach ($question as $answer)
                    { y: {{ $answer->amount }}, label: "{{ $answer->answer }}"},
                  @endforeach   
                ]
            }
            ]
        });

        chart.render();
    }
</script>