  <script type="text/javascript">
    /*
     * -----------------------------------------Map JS--------------------------
     */
    // Containing province id from click event
    var FilterSelect = {
      region:"",
      category:{{ $default_question->id_question_categories }},
      question: {{ $default_question->id_question }},
      question_code: {{ $default_question->question_code }},
      cycle:{{ $default_question->id_cycle }},
      answers:[
        @foreach ($question as $key_answers => $answer)
          { id: {{ $answer->id_answer }} ,answer: "{{ $answer->answer }}"},
        @endforeach
      ]
    };

    var DefaultSelect = {
      region:"",
      category:{{ $default_question->id_question_categories }},
      question: {{ $default_question->id_question }},
      question_code: {{ $default_question->question_code }},
      cycle:{{ $default_question->id_cycle }},
      answers:[
        @foreach ($question as $key_answers => $answer)
          { id: {{ $answer->id_answer }} ,answer: "{{ $answer->answer }}"},
        @endforeach
      ]
    };

    var dynamicRegions = [];

    // Re assign Default Filter Select
    function DefaultSelectAssign(data) {
      DefaultSelect = {
        region: data.region,
        category: data.category,
        question: data.question,
        question_code: data.question_code,
        cycle: data.cycle,
        answers:data.answers
      };
    }
$( document ).ready(function() {
    // // Removed last clicked area
    // var lastClickedLayer;
    // // Map Centering
    // var map = L.map('map');

    // L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
    //   maxZoom: 18,
    //   zoomControl:false,
    //   id: 'examples.map-20v6611k'
    // }).addTo(map);

    // // get color depending on highest maximum vote
    // function getColor(d) {
    //   var color = 'white';
    //   @foreach ($regions as $key_region => $region)
    //     if (d === '{{ $region["name"] }}')
    //     {
    //       color = '{{ $region["color"] }}';
    //     }
    //   @endforeach
    //   return color;
    // }
    // function style(feature) {
    //   return {
    //     weight: 2,
    //     opacity: 0.7,
    //     color: '#8E73F1',
    //     dashArray: '3',
    //     fillOpacity: 0.7,
    //     fillColor: getColor(feature.properties.nm_provinsi)
    //   };
    // }
    // var geojson;
    // var popupRegion;

    // function onEachFeature(feature, layer) {
    //   // layer.on({
    //   //   mouseover: hoverHightlight,
    //   //   mouseout: outHightlight,
    //   //   click: AddHighlight
    //   // });
    // }

    // geojson = L.geoJson(statesData, {
    //   style: style,
    //   onEachFeature: onEachFeature,
    // }).addTo(map);


    // map.attributionControl.addAttribution('Asia Foundation Survey');
    // // Auto Center
    // map.fitBounds(geojson.getBounds());
    // // Disable drag and zoom handlers.
    // map.touchZoom.disable();
    // map.doubleClickZoom.disable();
    // map.scrollWheelZoom.disable();

    });
    /*
     * -----------------------------------------End Map JS-----------------------------------------
     */
    function cycle_select_option(survey_id){
    $.get( "{{ URL::to('/survey/cyclelist') }}", { survey_id: survey_id,cycle_id : $("#cycle_select_modal").val()})
      .done(function( data ) {
        $("#question_select_modal").html(data);
      });
      return false;
    }
  </script>