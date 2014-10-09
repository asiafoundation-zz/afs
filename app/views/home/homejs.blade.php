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


    map.attributionControl.addAttribution('Asia Foundation Survey');
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
  </script>
