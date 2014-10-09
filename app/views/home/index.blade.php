@include('layouts/header')

  <section class="map">
    <a class="logo" href="#"><img src="{{ Theme::asset('img/logo.png') }}"></a>
    <div class="border-top"></div>
    <div class="top-nav">
      <div class="right-side">
        <p>2013</p>
        <div class="dropdown">
          <a href="#" class="hamburger"></a>
        </div>
      </div>
    </div>
    <div class="dropshadow">
      <img src="{{ Theme::asset('img/dropshadow.png') }}">
      <div class="search-wrp">
        <div class="col-md-3">
          <a href="#" id="category">
            <img src="{{ Theme::asset('img/add.png') }}" />
            <span>{{Lang::get('frontend.select_category')}}</span>
          </a>
          <div class="dropdown-path">
              <ul class="dropdown-scroll">
                <li><a onclick='select_category(1)' id="select_category_id_1" >Default Category</a></li>
              </ul>
            <span class="arrow-down"></span>
          </div>
        </div>
        <div class="col-md-3">
          <a href="#" id="question">
            <img src="{{ Theme::asset('img/add.png') }}" />
            <span>{{Lang::get('frontend.select_question')}}</span>
          </a>
          <div class="dropdown-path">
            <ul class="dropdown-scroll">
                <li><a onclick='select_question(1)' id="select_question_id_1" >Persepsi Mengenai Pemilu</a></li>
                <li><a onclick='select_question(2)' id="select_question_id_2" >Kebutuhan pendidikan kewarganegaraan</a></li>
                <li><a onclick='select_question(3)' id="select_question_id_3" >Pemahaman mengenai daftar pemilih</a></li>
            </ul>
            <span class="arrow-down"></span>
          </div>
        </div>
        <div class="col-md-6"><a class="find-surveys" href="#" onclick='find_survey()'>{{Lang::get('frontend.find_surveys')}} <img src="{{ Theme::asset('img/arrow.png') }}"></a></div>
      </div>
    </div>
    <div id="map" class="map-canvas" style="position: absolute; right: 0px; top: 0px; width: 100%; height: 670px"></div>
  </section>

  <section class="filter">
    <div class="container">
      <div class="col-md-12">
        <ul>
          @foreach ($filters as $key_filters => $filter)
          <li>
            <select class="select-control">
              <option>{{ $key_filters }}</option>
              @foreach ($filter as $filter_items)
              <option>{{ $filter_items }}</option>
              @endforeach
            </select><!-- Custom Select -->
          </li>
          @endforeach
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
        <h3>{{Lang::get('frontend.survey_question')}}</h3>
        <p>Alasan utama yang menyebabkan orang-orang tidak <br/>mengikuti Pemilihan Presiden pada bulan Juli 2014</p>
        <div class="chart">
          <div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div>
          <div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>
          <div class="col-md-12">
            <ul class="chart-pagination">
              <li><a href="#" class="orange-bg"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li>
              <li><a href="#" class="orange-bg">{{Lang::get('frontend.compare_this_survey')}}</a></li>
              <li><a href="#" class="orange-bg"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="compare-survey">
    <div class="container">
      <div class="col-md-6">
        <h4>Lorem ipsum dolor sit amet, consectet</h4>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus, dignissim vel arcu sit amet, sodales dignissim nibh. Suspendisse lobortis neque sed est sollicitudin ornare.<br><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus, dignissim vel arcu sit amet, sodales dignissim nibh. Suspendisse lobortis neque sed est sollicitudin ornare.</p>
      </div>
      <div class="col-md-6">
        <div class="extras">
          <img src="{{ Theme::asset('img/compare.png') }}">
          <div>
            <h4>Compare Survey Results</h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus</p>
            <a href="#" class="orange-bg">COMPARE SURVEY</a>
          </div>
        </div>
        <div class="extras">
          <img src="{{ Theme::asset('img/variable.png') }}">
          <div>
            <h4>Cross by Another Variable</h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus</p>
            <a href="#" class="orange-bg">Cross by another variable</a>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  @include('home/homejs')
</body>
</html>