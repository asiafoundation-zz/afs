<!DOCTYPE html>
<html id="newhome" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Site Properities -->
    <title>{{ Setting::meta_data('general', 'name')->value }} - {{ Setting::meta_data('general', 'tag_line')->value }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/style.css') }}">

    <script src="{{ Theme::asset('javascript/jquery-1.7.2.min.js') }}"></script>
    <script src="{{ Theme::asset('javascript/modernizr-2.6.2.min.js') }}"></script>


    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<!--
    <script src="http://openlayers.org/en/v3.0.0/build/ol.js" type="text/javascript"></script>
    <link rel="stylesheet" href="http://openlayers.org/en/v3.0.0/css/ol.css" type="text/css">

    <script src="http://maps.google.com/maps/api/js?v=3.6&amp;sensor=false"></script>
    <script src="http://www.openlayers.org/api/OpenLayers.js"></script>
-->
    <!-- Map JS-->
    <script src="{{ Theme::asset('javascript/leaflet.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ Theme::asset('css/leaflet.css') }}">
    <script type="text/javascript" src="{{ Theme::asset('geojson/geojson.geojson') }}"></script>
  </head>

  <body>
  
  <section class="map">
    <div class="border-top"></div>
    <div id="map" class="map"></div>
    <div class="container">
      <a class="logo" href=""><img src="{{ Theme::asset('img/logo.png') }}"></a>

      <div class="dropshadow">
        <img src="{{ Theme::asset('img/dropshadow.png') }}">
        <div class="search-wrp">
          <div class="col-md-3">
            <a href="">
              <img src="{{ Theme::asset('img/add.png') }}" />
              <span id="select_category_label">{{Lang::get('frontend.select_category')}}</span>
            </a>
            <div class="dropdown-path">
              <ul class="dropdown-scroll">
                <li><a onclick='select_category(1)' id="select_category_id_1" >Default Category</a></li>
              </ul>
              <span class="arrow-down"></span>
            </div>
          </div>
          <div class="col-md-6">
            <a href="">
              <img src="{{ Theme::asset('img/add.png') }}" />
              <span id="select_question_label">{{Lang::get('frontend.select_question')}}</span>
            </a>
            <div class="dropdown-path">
              <ul class="dropdown-scroll">
                <li><a onclick='select_question(1)' id="select_question_id_1" >Persepsi Mengenai Pemilu</a></li>
                <li><a onclick='select_question(2)' id="select_question_id_2" >Kebutuhan pendidikan kewarganegaraan</a></li>
                <li><a onclick='select_question(3)' id="select_question_id_3" >Pemahaman mengenai daftar pemilih</a></li>
                <li><a onclick='select_question(4)' id="select_question_id_4" >Persepsi Mengenai Pemilu</a></li>
                <li><a onclick='select_question(5)' id="select_question_id_5" >Kebutuhan pendidikan kewarganegaraan</a></li>
                <li><a onclick='select_question(6)' id="select_question_id_6" >Pemahaman mengenai daftar pemilih</a></li>
              </ul>
              <span class="arrow-down"></span>
            </div>
          </div>
          <div class="col-md-3"><a class="find-surveys" href="#" onclick='find_survey()'>{{Lang::get('frontend.find_surveys')}} <img src="{{ Theme::asset('img/arrow.png') }}"></a></div>
        </div>
      </div>
    </div>
  </section>

  <section class="filter">
    <div class="container">
      <div class="col-md-12">
        <ul>
          <li>
            <a href="">
              <img src="{{ Theme::asset('img/filter.png') }}">
              <span>{{Lang::get('frontend.filter_by_age')}}</span>
            </a>
          </li>
          <li>
            <a href="">
              <img src="{{ Theme::asset('img/filter.png') }}">
              <span>{{Lang::get('frontend.filter_by_income')}}</span>
            </a>
          </li>
          <li>
            <a href="">
              <img src="{{ Theme::asset('img/filter.png') }}">
              <span>{{Lang::get('frontend.filter_by_education')}}</span>
            </a>
          </li>
          <li>
            <a href="">
              <img src="{{ Theme::asset('img/filter.png') }}">
              <span>{{Lang::get('frontend.filter_by_gender')}}</span>
            </a>
          </li>
          <li>
            <a class="clear-all" onclick='clear_all_filter()' href="#">{{Lang::get('frontend.clear_all')}}</a>
          </li>
        </ul>
      </div>
    </div>
  </section>
  
  <section class="survey-pemilu">
    <div class="container center">
      <div class="col-md-12">
        <h1>Hasil Survey Pemilu 2013/2014</h1>
        <h3>Survey Question</h3>
        <p>Alasan utama yang menyebabkan orang-orang tidak <br/>mengikuti Pemilihan Presiden pada bulan Juli 2014</p>
        <div class="chart">
          <div class="col-md-5"><div id="chartContainerPie" style="height: 225px; width: 100%;"></div></div>
          <div class="col-md-7"><div id="chartContainer" style="height: 225px; width: 100%;"></div></div>
        </div>
      </div>
    </div>
  </section>

  <section class="compare-survey">
    <div class="container">
      <div class="col-md-6">
        <h4>Lorem ipsum dolor sit amet, consectet</h4>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus, dignissim vel arcu sit amet, sodales dignissim nibh. Suspendisse lobortis neque sed est sollicitudin ornare.</p>
      </div>
      <div class="col-md-6">
        <img src="{{ Theme::asset('img/compare.png') }}">
        <div>
          <h4>Compare Survey Results</h4>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus</p>
          <a href="" class="orange-bg">COMPARE SURVEY</a>
        </div>
      </div>
    </div>
  </section>
  
  <footer>
    <div class="container center">
      <div class="col-md-12">
        <a href=""><img src="{{ Theme::asset('img/logo-footer.png') }}"></a>
        <p>Survey Q Copyright 2014. All rights reserved.</p>
      </div>
    </div>
  </footer>


  <script type="text/javascript" src="{{ Theme::asset('javascript/bootstrap.min.js') }}"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
  <script type="text/javascript" src="{{ Theme::asset('javascript/query.ui.touch-punch.min.js') }}"></script>
  <script type="text/javascript" src="{{ Theme::asset('javascript/facescroll.js') }}"></script>
  <script type="text/javascript" src="{{ Theme::asset('javascript/canvasjs.min.js') }}"></script>
  <script type="text/javascript" src="{{ Theme::asset('javascript/mapbbcode.js') }}"></script>

  <script type="text/javascript">

    /*
     * -----------------------------------------Map JS--------------------------
     */
    // Containing province id from click event
    var ClickMapRegion = {
      region:null,
      category:null,
      question:null,
    };
    // Removed last clicked area
    var lastClickedLayer;
    // Map Centering
    var map = L.map('map');

    L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
      maxZoom: 18,
      zoomControl:false,
      attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
        '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="http://mapbox.com">Mapbox</a>',
      id: 'examples.map-20v6611k'
    }).addTo(map);

    // get color depending on population id_provinsi value
    function getColor(d) {
      return d == 64 ? '#800026' :
             d == 65  ? '#BD0026' :
             d == 32  ? '#E31A1C' :
             d == 15  ? '#FC4E2A' :
             d == 75   ? '#FD8D3C' :
             d == 31   ? '#FEB24C' :
             d == 34   ? '#FED976' :
                        'white';
    }

    function style(feature) {
      return {
        weight: 2,
        opacity: 0.7,
        color: '#8E73F1',
        dashArray: '3',
        fillOpacity: 0.7,
        fillColor: getColor(feature.properties.id_provinsi)
      };
    }

    function highlightFeature(e) {
      var layer = e.target;

      layer.setStyle({
        weight: 3,
        opacity: 1,
        dashArray: '1',
        fillOpacity: 0.7,
        fillColor: "#B2B0B8"
      });

      if (!L.Browser.ie && !L.Browser.opera) {
        layer.bringToFront();
      }
    }

    var geojson;
    var popupRegion;

    function resetHighlight(e) {
      ClickMapRegion.region = null;
      geojson.resetStyle(e.target);
    }

    function AddHighlight(e) {
      if(lastClickedLayer){
         geojson.resetStyle(lastClickedLayer);
      }

      var layer = e.target;

      ClickMapRegion.region = null;
      ClickMapRegion.region = layer.feature.properties.id_provinsi;
      highlightFeature(e);

      lastClickedLayer = layer;

      console.log(ClickMapRegion);
    }

    function hoverHightlight(e){
      var layer = e.target;

      popupRegion = L.marker([e.latlng.lat, e.latlng.lng], { icon: L.popupIcon(layer.feature.properties.nm_provinsi), clickable: false }).addTo(map);
    }

    function outHightlight(e){
      map.removeLayer(popupRegion);
    }

    function onEachFeature(feature, layer) {
      layer.on({
        mouseover: hoverHightlight,
        mouseout: outHightlight,
        click: AddHighlight,
        dblclick: resetHighlight
      });
    }

    geojson = L.geoJson(statesData, {
      style: style,
      onEachFeature: onEachFeature,
    }).addTo(map);


    map.attributionControl.addAttribution('Asia Survey Foundation');
    // Auto Center
    map.fitBounds(geojson.getBounds());
    // Disable drag and zoom handlers.
    map.touchZoom.disable();
    map.doubleClickZoom.disable();
    map.scrollWheelZoom.disable();
    /*
     * -----------------------------------------End Map JS-----------------------------------------
     */
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

    $('.dropdown-scroll').alternateScroll({ 'vertical-bar-class': 'styled-v-bar', 'hide-bars': false });

    $( window ).load(function() {
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
            type: "pie",
            indexLabelFontFamily: "Garamond",       
            indexLabelFontSize: 0,
            startAngle:0,
            indexLabelFontColor: "MistyRose",       
            indexLabelLineColor: "#fff", 
            indexLabelPlacement: "inside", 
            toolTipContent: "{name}: {y}hrs",
            showInLegend: false,
            // indexLabel: "#percent%", 
            dataPoints: [
              // {  y: 52, name: "Time At Work", legendMarkerType: "triangle"},
              // {  y: 44, name: "Time At Home", legendMarkerType: "square"},
              // {  y: 12, name: "Time Spent Out", legendMarkerType: "circle"}

              { y: 2, legendMarkerType: "Tidak percaya pemilu", name: "2%" },
              { y: 42, legendMarkerType: "Malas", name: "42%" },
              { y: 18, legendMarkerType: "Bingung dengan pilihan", name: "18%" },
              { y: 4, legendMarkerType: "Tidak tahu adanya pemilu", name: "4%" },                    
              { y: 34, legendMarkerType: "Berhalangan", name: "34%" }
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
    });
  </script>

  
</body>
</html>