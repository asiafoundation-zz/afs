  <footer>
    <div class="container center">
      <div class="col-md-12">
        <a href="#"><img src="{{ Theme::asset('img/logo-footer.png') }}"></a>
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
  <script type="text/javascript" src="{{ Theme::asset('javascript/base.js') }}"></script>
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
  </script>

  
</body>
</html>