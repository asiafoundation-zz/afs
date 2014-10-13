  <script type="text/javascript">
    /*
     * -----------------------------------------Map JS--------------------------
     */
    // Containing province id from click event
    var FilterSelect = {
      region:null,
      category:{{ $default_question->id_question_categories }},
      question:{{ $default_question->id_question }},
      cycle:{{ $default_question->id_cycle }}
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

    // get color depending on highest maximum vote
    function getColor(d) {
      var color = 'white';
      @foreach ($regions as $key_region => $region)
        if (d == '{{ $key_region }}')
        {
          color = '{{ $region["color"] }}';
        }
      @endforeach
      return color;
    }

    function style(feature) {
      return {
        weight: 2,
        opacity: 0.7,
        color: '#8E73F1',
        dashArray: '3',
        fillOpacity: 0.7,
        fillColor: getColor(feature.properties.nm_provinsi)
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
      FilterSelect.region = null;
      geojson.resetStyle(e.target);
    }

    function AddHighlight(e) {
      if(lastClickedLayer){
         geojson.resetStyle(lastClickedLayer);
      }

      var layer = e.target;

      FilterSelect.region = null;
      FilterSelect.region = layer.feature.properties.nm_provinsi;
      highlightFeature(e);
      LoadDataHighligtArea(layer);

      lastClickedLayer = layer;

      console.log(FilterSelect);
    }

    // Load Queston and Categories based on Area
    function LoadDataHighligtArea(e) {
      $.get( "filter-select", { SelectedFilter:"area", region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( response ) {
            var data = response.split(";");
            $("#div-filter-category").html(data[0]);
            $("#div-filter-question").html(data[1]);
          });
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
  </script>
