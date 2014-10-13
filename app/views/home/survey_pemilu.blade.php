  <section class="survey-pemilu">
    <div class="container center">
      <div class="col-md-12">
        <h1>{{ $survey->name }}</h1>
        <h3>{{Lang::get('frontend.survey_question')}}</h3>
        <p>{{ $default_question->question }}</p>
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