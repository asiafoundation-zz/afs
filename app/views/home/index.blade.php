@extends('layouts/default')

@section('content')

  @include('partial/homeasset')
  <section class="header">
    <div class="container">
      <p>{{ $survey->name }}</p>
    </div>
  </section>

  <section class="map">
    <div class="top-nav">
      <div class="left-side">
        <p id="select_region_label"></p>
      </div>
    </div>
    <div class="search-wrp header-select">
      <!-- <select class="select-control select-cycle"> -->
      <select class="select2-custom select-cycle">
        <option>{{ Lang::get('frontend.cycle') }}</option>
        @foreach ($cycles as $cycle)
        <option value="{{ $cycle->id }}">{{ $cycle->name }}</option>
        @endforeach
      </select><!-- Custom Select -->
      <select class="select2-custom select-category">
        <option>{{ Lang::get('frontend.select_category') }}</option>
        @foreach ($question_categories as $question_category)
        <option value="{{ $question_category->id }}">{{ $question_category->name }}</option>
        @endforeach
      </select><!-- Custom Select -->
      <select class="select2-custom select-question">
        <option>{{ Lang::get('frontend.select_question') }}</option>
        @foreach ($question_lists as $question_list)
        <option value="{{ $question_list->id }}"> - {{ $question_list->question }}</option>
        @endforeach
      </select><!-- Custom Select -->
    </div>
    <div id="map" class="map-canvas" style="position: absolute; right: 0px; top: 0px; width: 100%; height: 100%"></div>
  </section>

  <section class="filter">
    <div class="container">
      <div class="col-md-12 dropdown-filter">
        <!-- <div class="col-xs-6 col-sm-3">Filter Participant by</div> -->
        <ul>
<!--           <li>
            <select class="select-control">
              <option class="selectik-filter-region">{{ Lang::get('frontend.all_region') }}</option>
              @foreach ($regions as $region)
              <option value="{{ $region['region_id'] }}" class="selectik-filter-region">{{ $region['name'] }}</option>
              @endforeach
            </select>
          </li> -->
          @foreach ($filters as $key_filters => $filter)
          <li>
            <select class="select-control">
              <option>{{ $filter['category_name'] }}</option>
              @foreach ($filter['category_items'] as $filter_items)
              <option value="{{ $filter_items['category_item_id'] }}">{{ $filter_items['category_item_name'] }}</option>
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
        <h3>{{Lang::get('frontend.survey_question')}}</h3>
        <p id="question-name">" {{ $default_question->question }} "</p>
        @include('home/cross_question')
        <div class="chart chart-flag">
          <div id="chart_canvas">
            <div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div>
            <div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>
          </div>
          <div class="col-md-12">
            <ul class="chart-pagination">
            @if(count($cycles) > 1)
              <li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"></a></li>
              <li id="chart_pagination_text"><a class="orange-bg" onclick="compare_cycle(0)">{{Lang::get('frontend.compare_this_survey')}}</a></li>
              <li><a class="orange-bg" onclick="next_question(1)"><img src="{{ Theme::asset('img/arrow.png') }}"></a></li>
            @endif
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="compare-survey">
    <div class="container">
      <div class="col-md-6">
        <h4>{{ Lang::get('frontend.description') }}</h4>
        <p>{{ Lang::get('frontend.description_content') }}</p>
      </div>
      <div class="col-md-6">
        <div class="extras">
          <img src="{{ Theme::asset('img/compare.png') }}">
          <div>
            <h4>{{Lang::get('frontend.compare_survey_results')}}</h4>
            <p>{{ Lang::get('frontend.compare_survey_results_content') }}</p>
            <a onclick='compare_cycle(2)' class="orange-bg">{{Lang::get('frontend.compare_survey')}}</a>
          </div>
        </div>
        <div class="extras">
          <img src="{{ Theme::asset('img/variable.png') }}">
          <div>
            <h4>{{Lang::get('frontend.cross_by_another_variable')}}</h4>
            <p>{{ Lang::get('frontend.cross_by_another_variable_p') }}</p>
            <a href="#" class="orange-bg show-cross">{{Lang::get('frontend.cross_by_another_variable')}}</a>
          </div>
        </div>
      </div>
    </div>
  </section>

@include('partial/homefooter')

@stop