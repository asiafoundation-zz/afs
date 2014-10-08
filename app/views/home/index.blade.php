@include('layouts/default')

  <section class="map">
    <div class="border-top"></div>
    <div id="map" class="map-canvas"></div>
    <div class="container">
      <a class="logo" href=""><img src="{{ Theme::asset('img/logo.png') }}"></a>
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
            <a href="#" id="question">
              <img src="{{ Theme::asset('img/add.png') }}" />
              <span id="select_question_label">{{Lang::get('frontend.select_question')}}</span>
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
          <div class="col-md-3"><a class="find-surveys" href="#" onclick='find_survey()'>{{Lang::get('frontend.find_surveys')}} <img src="{{ Theme::asset('img/arrow.png') }}"></a></div>
        </div>
      </div>
  </section>

  <section class="filter">
    <div class="container">
      <div class="col-md-12">
        <ul>
          <li>
            <a href="#">
              <img src="{{ Theme::asset('img/filter.png') }}">
              <span>{{Lang::get('frontend.cycle')}}</span>
            </a>
          </li>
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
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus, dignissim vel arcu sit amet, sodales dignissim nibh. Suspendisse lobortis neque sed est sollicitudin ornare.</p>
      </div>
      <div class="col-md-6">
        <img src="{{ Theme::asset('img/compare.png') }}">
        <div>
          <h4>{{Lang::get('frontend.compare_survey_results')}}</h4>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ipsum risus</p>
          <a href="" class="orange-bg">{{Lang::get('frontend.compare_survey')}}</a>
        </div>
      </div>
    </div>
  </section>
  
@include('layouts/footer')