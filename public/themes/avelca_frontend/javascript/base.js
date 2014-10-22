function compare_cycle(category_id)
{
  // Get cycles functions
  $.get( "filter-select", { SelectedFilter:"compare_cycle",region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle, answers:FilterSelect.answers} )
    .done(function( data ) {
      if (data != false) {
        $("#chart_canvas").html('<div class="col-md-12"><div id="compareChart" style="height: 345px; width: 100%;"></div></div>');
        compare_chart(data.question);
      }else
      {
        alert("{{Lang::get('frontend.empty_data')}}");
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
      }else
      {
        alert("{{Lang::get('frontend.empty_data')}}");
      }
    },"html");
}
/*
* -----------------------------------------Chart JS--------------------------
*/
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
      }
      ]
  });

  chartbar.render();
}

function compare_chart(data)
{
  var baseline_list = [];
  var endline_list = [];

  for (i = 0; i < data.length; i++) {
    if (data[i].cycle_type == 0) {
      var baseline_text = data[i].cycle;
      baseline_list.push({ y: data[i].amount, label: data[i].answer});
    }
    if (data[i].cycle_type == 1) {
      var endline_text = data[i].cycle;
      endline_list.push({ y: data[i].amount, label: data[i].answer});
    }
  };

  CanvasJS.addColorSet("greenShades",[//colorSet Array
        "#fcc45a",
        "#ffe87a",
        "#abd074",
        "#fc5b3f",
        "#1eb5b6"                
        ]);

  var chart = new CanvasJS.Chart("compareChart",
  {
      title:{
        text: "Compares "+baseline_text+" and "+endline_text
      },
      colorSet: "greenShades",
      data: [
      {
        type: "bar",
        showInLegend: true,
        legendText: baseline_text,
        dataPoints: baseline_list
      },
      {
        type: "bar",
        showInLegend: true,
        legendText: endline_text,
        dataPoints: endline_list
      }
      ]
  });

  chart.render();
}
/*
* -----------------------------------------End Chart JS--------------------------
*/
/*
* -----------------------------------------Filter Category JS--------------------------
*/
function select_question(question_id)
{
  var question_text = $("#select_question_id_"+question_id).text();
  FilterSelect.question = question_text;
  $("#select_question_label").html(question_text);
}

function select_category(category_id)
{
  FilterSelect.category = category_id;
  var category_text = $("#select_category_id_"+category_id).text();
  $("#select_category_label").html(category_text);
}

function clear_all_filter()
{
  window.location.reload();
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