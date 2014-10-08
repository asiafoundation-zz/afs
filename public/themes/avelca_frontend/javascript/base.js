    /*
     * -----------------------------------------Filter Category JS--------------------------
     */
     function find_survey()
     {
        alert("region choose ="+ClickMapRegion.region+";category choose ="+ClickMapRegion.category+";question choose ="+ClickMapRegion.question);
     }
     function select_question(question_id)
     {
        ClickMapRegion.question = question_id;
        var question_text = $("#select_question_id_"+question_id).text();
        $("#select_question_label").html(question_text);
     }
     function select_category(category_id)
     {
        ClickMapRegion.category = category_id;
        var category_text = $("#select_category_id_"+category_id).text();
        $("#select_category_label").html(category_text);
     }
     function clear_all_filter()
     {
      ClickMapRegion = {
        category:null,
        question:null,
      };
     }
    /*
     * -----------------------------------------END Filter Category  JS--------------------------
     */

    $('.search-wrp > div > a').click(function(){
      $(this).siblings('.dropdown-path').show();
      return false;
    })

    $('body').click(function(){
      $('.dropdown-path').hide();
    })

    $('.search-wrp > div > a#question').click(function(){
      $('.search-wrp > div > a#category + .dropdown-path').hide();
    })

    $('.search-wrp > div > a#category').click(function(){
      $('.search-wrp > div > a#question + .dropdown-path').hide();
    })

    $('.dropdown-scroll').alternateScroll({ 'vertical-bar-class': 'styled-v-bar', 'hide-bars': false });

    window.onload = function () {

        // PIE CHART

        CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                "#fcc45a",
                "#ffe87a",
                "#abd074",
                "#fc5b3f",
                "#1eb5b6"                
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
              // {  y: 52, name: "Time At Work", legendMarkerType: "triangle"},
              // {  y: 44, name: "Time At Home", legendMarkerType: "square"},
              // {  y: 12, name: "Time Spent Out", legendMarkerType: "circle"}

              { y: 2, label: "Tidak percaya pemilu"},
              { y: 42, label: "Malas"},
              { y: 18, label: "Bingung dengan pilihan"},
              { y: 4, label: "Tidak tahu adanya pemilu"},
              { y: 34, label: "Berhalangan" }
            ]
          }
          ]
        });
        chart.render();


        // BAR CHART

        CanvasJS.addColorSet("greenShades",
                [//colorSet Array

                "#fcc45a",
                "#ffe87a",
                "#abd074",
                "#fc5b3f",
                "#1eb5b6"                
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
                    { y: 2, label: "Tidak percaya pemilu", indexLabel: "2%" },
                    { y: 42, label: "Malas", indexLabel: "42%" },
                    { y: 18, label: "Bingung dengan pilihan", indexLabel: "18%" },
                    { y: 4, label: "Tidak tahu adanya pemilu", indexLabel: "4%" },                    
                    { y: 34, label: "Berhalangan", indexLabel: "34%" }


                ]
            }
            ]
        });

        chart.render();
    }