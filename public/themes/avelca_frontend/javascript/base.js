/*
* -----------------------------------------Chart JS--------------------------
*/
function chartjs(color_set,data_points,data_points_pie)
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
      indexLabelFontColor: "#ffffff",       
      indexLabelPlacement: "inside", 
      toolTipContent: "{name}: {y} - <strong>#percent%</strong>",
      showInLegend: false,
      indexLabel: " ", 
      dataPoints: data_points_pie
    }
    ]
  });
  chart.render();

  // BAR CHART
  // var total_participant = 0;
  // for (i = 0; i < data_points.length; i++) {
  //   total_participant = data_points[i].y >= total_participant ? data_points[i].y : total_participant;
  // };
  // // Set Interval
  // var interval = total_participant > 10 ? Math.floor(total_participant / 10) : 1;

  // Set width
  var width = 0;
  var label_font_size = 10;
  if (data_points.length <= 10) {
    width = 300;
  }else if(data_points.length > 10 && data_points.length <= 20){
    width = 600;
    label_font_size = 14;
  }else{
    width = Math.floor(data_points.length / 10);
    width = width * 400;
    label_font_size = 14;
  }

  // Apply width to chart
  $("#chartContainer").css({'height': width+'px', 'width':'100%'});
  CanvasJS.addColorSet("greenShades",color_set);
  var chartbar = new CanvasJS.Chart("chartContainer", {
      colorSet: "greenShades",
      axisY: {
        // maximum: 100,
        minimum:0,
        interval: 10,
        tickLength: 0,
        gridThickness: 1,
        labelFontSize: 10,
      },
      axisX: {
          interval: 1,
          tickThickness: 1,
          lineThickness: 1,
          labelFontSize: label_font_size,
          labelMaxWidth: 200,
          labelWrap: false,
          sufix: "... "
      },
      data: [
      {
          indexLabelFontSize: 12,
          labelFontFamily: "DINNextLTPro-Regular",
          indexLabelOrientation: "horizontal",
          labelFontColor: "gray",
          indexLabelFontColor: "gray",
          indexLabelFontFamily: "DINNextLTPro-Regular",
          indexLabelPlacement:"inside",
          toolTipContent: "{name}: <strong>{y}%</strong>",
          type: "bar",
          click: function(e){
            detail_chart(e.dataPoint.answer_id,0,0)
           },
          dataPoints: data_points
      }
      ]
  });

  chartbar.render();
}

function compare_chart(first_list, end_list, colorSet, baseline_text,endline_text)
{
  // console.log(first_list);
  // console.log(end_list);

  var width = 0;
  var label_font_size = 10;
  if (first_list.length <= 10) {
    width = 300;
  }else if(first_list.length > 10 && first_list.length <= 20){
    width = 600;
    label_font_size = 18;
  }else{
    width = Math.floor(first_list.length / 10);
    width = width * 400;
    label_font_size = 20;
  }

  $("#compareChart").css({'height': width+'px', 'width':'100%'});

  CanvasJS.addColorSet("greenShades",colorSet);
  // console.log(first_list);
  var chart = new CanvasJS.Chart("compareChart",
  {
      title:{
        text: 'Compare "'+baseline_text+'" dan "'+endline_text+'"',
        fontSize: 24
      },
      axisX: {
        labelFontSize: label_font_size,
        interval:1
      },
      axisY: {
        labelFontSize: label_font_size
      },
      // axisY2: {
      //   labelFontSize: label_font_size
      // },
      legend: {
        fontSize: 24,
      },
      colorSet: "greenShades",
      data: [
      {
        type: "bar",
        showInLegend: true,
        legendText: baseline_text+" (Hasil survey 2013)",
        dataPoints: first_list,
        toolTipContent: "{name}: <strong>{y}%</strong>"
      },
      {
        type: "bar",
        showInLegend: true,
        // axisYType: "secondary",
        legendText: endline_text+" (Hasil survey 2014)",
        dataPoints: end_list,
        toolTipContent: "{name}: <strong>{y}%</strong>"
      }
      ]
  });

  chart.render();
}

function detail_chart_js(data)
{
  var data_list = [];
  var total_participant = 10;

  for (i = 0; i < data.length; i++) {
    // Cut String
    var data_text = data[i].category_name;
    var label = data[i].category_item_name;

    var amount = 0;
    if (data[i].amount === null){
      amount = 0;
    }else{
      amount = data[i].amount;
    }

    if (label.length > 12){
      label = label.substr(0, 12);
      label = label+" ...";
    }
    total_participant += parseInt(amount); 
    data_list.push({ y: parseInt(amount), indexLabel: data[i].indexlabel+"%", label: label});
  }
  
  CanvasJS.addColorSet("hellowYellow",
  [//colorSet Array
    "#ffe87a"              
  ]);

  var chart = new CanvasJS.Chart("detailChart",
  {
    theme: "theme1",
    title:{
      text: data_text,
      fontSize: 24,
      fontWeight: "lighter",
      margin: 30,
      padding: 0
    },
    axisY: {
      maximum: total_participant,
      tickLength: 0,
      gridThickness: 1
    },
    axisX: {
      labelFontSize: 12,
      tickLength: 10
    },
    colorSet: "hellowYellow",
    data: [
    {        
      type: "column",  
      showInLegend: false, 
      indexLabelPlacement: "outside",  
      labelFontFamily: "DINNextLTPro-Regular",
      dataPoints: data_list
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
  FilterSelect.question = question_id;
  $("#select_question_label").html(question_text.slice(0,40)+" ...");
}

function select_category(category_id)
{
  FilterSelect.category = category_id;
  var question_text = "Select Category";
  FilterSelect.question = 0;
  var category_text = $("#select_category_id_"+category_id).text();
  $("#select_category_label").html(category_text.slice(0,15)+" ...");
  $("#select_question_label").html(question_text);
  change_category();
}

function clear_all_filter_nosurvey(){
  FilterSelect.region = "";

   $(".dropdown-filter .selected_filter_option").each(function(){
     filter_text = $('.title-filters', $(this).parent('ul')).html();
     if (filter_text != null) {
       var display_text = filter_text.toUpperCase();
       filter_text = filter_text.toUpperCase().split(' ').join('_');

       /* Remove class selected li */
       $(this).removeClass('selected_filter_option');
       
       /* Add class to first li */
       $('#filter_option_label_'+ filter_text).addClass('selected_filter_option');

       /* Change filter text */
       $('#custom-text-title-'+filter_text).html("");
       $('#custom-text-title-'+filter_text).html(display_text);
     }
  });
  return false; 
}

function clear_all_filter_noregion(){
  $(".dropdown-filter .li-filter .selected_filter_option").each(function(){
     filter_text = $('.title-filters', $(this).parent('ul')).html();
     if (filter_text != null) {
       var display_text = filter_text.toUpperCase();
       filter_text = filter_text.toUpperCase().split(' ').join('_');

       /* Remove class selected li */
       $(this).removeClass('selected_filter_option');
       
       /* Add class to first li */
       $('#filter_option_label_'+ filter_text).addClass('selected_filter_option');

       /* Change filter text */
       $('#custom-text-title-'+filter_text).html("");
       $('#custom-text-title-'+filter_text).html(display_text);
     }
  });
  return false;
}

function find_survey_dynamic_select(region_id, type){
  var region = $("#filter_option_label_"+region_id);
  var value = [region_id, type];

  if(region.data('value') != null){
    FilterSelect.region = parseInt(region_id);
    
    if(FilterSelect.is_compare == 1){
      disable_anchor($('.clear-all'), '', 1);
      FilterSelect.filter_exist = 0;
      compare_cycle(0);
    }

    /* Detect filter exist or not. this also differentiate what filter is selected first */
    if(FilterSelect.filter_exist == 0){
      find_survey_dynamic(value);
    }else{
      filter_option(region_id, type);
    }
    
  }
  else{
    FilterSelect.region = "";
    disable_anchor($('.clear-all'), '#AA6071', 0);

    if(FilterSelect.is_compare == 1){
      FilterSelect.filter_exist = 0;
      compare_cycle(0);
      return false;
    }

    if(FilterSelect.filter_exist == 0){
      find_survey();
    }else{
      filter_option(region_id, type);
    }

  }
}

function disable_anchor(selector, color, enable_flag){
  FilterSelect.filter_exist = 0;
  
  if(enable_flag == 1){

    if(color != ""){
      var background = color
    }else{
      var background = "";
    }

    selector.css({
      'pointer-events' : '',
      'cursor' : '',
      'background' : background
    });    
  }else{
    selector.css({
      'pointer-events' : 'none',
      'cursor' : 'default',
      'background' : color
    });
  }
}

function clear_filter(){
  FilterSelect.region = "";
  option_filters_default = [];
  filter_option(0);
  clear_all_filter_nosurvey();
  
  if(FilterSelect.is_compare == 1){
    compare_cycle(0);
  }else{
    find_survey();  
  }
 
  disable_anchor($('.clear-all'), '#AA6071', 0);
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

/*STICKY FILTER*/

// $(".sticky-filter").click(function(){
//       $("#filter").animate({left:'0'});
//       return false
//     }); 

//     $(".selectarea").click(function(){
//       $("#filter").animate({left:'-340px'});
//       $('html, body').animate({scrollTop : 0},800);
//       setTimeout(function(){$('.flash-message').css("display","block");}, 1400);
//       setTimeout(function(){$('.flash-message').css("display","none");}, 5000);
//       return false
//     });  

//     $(".close-stickyselect").click(function(){
//       $("#filter").animate({left:'-340px'});
//       return false;
//     });

    // $(".msdd").msDropDown();