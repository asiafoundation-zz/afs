@extends('layouts/default')

@section('content')

  @include('partial/homeasset')
  <a href="" class="sticky-filter"></a>

  <section class="header">
      <p>{{ $survey->name }}</p>
  </section>
  <!-- <section class="header">
    <div class="container">
      <p>{{ $survey->name }}</p>
    </div>
  </section> -->

<!--Update-->
<div class="elheader-wrapper"> <!-- add element wrapper - 28112014 -->
  <div class="search-wrp header-select">
    <select class="select2-custom select-cycle" id="select-cycle">
      @foreach ($cycles as $cycle)
      <option value="{{ $cycle->id }}" @if( $default_question->id_cycle == $cycle->id) selected @endif>{{ $cycle->name }}</option>
      @endforeach
    </select><!-- Custom Select -->
    <select class="select2-custom select-category" id="select-category">
      @foreach ($question_categories as $question_category)
      <option value="{{ $question_category->id }}" @if( $default_question->id_question_categories == $question_category->id) selected @endif >{{ $question_category->name }}</option>
      @endforeach 
    </select><!-- Custom Select -->
    <select class="select2-custom select-question" id="select-question">
      <option value="{{ $default_question->id_question }}">{{ $default_question->question }}</option>
    </select><!-- Custom Select -->
  </div>
</div> 

<!--End Update-->

  <section class="map">
    <div class="top-nav">
      <div class="left-side">
        <p id="select_region_label"></p>
      </div>
    </div>

    <!--Update-->
    <div class="flash-message">
      <p>Silahkan pilih salah satu provinsi untuk melanjutkan ke hasil survey</p>
      <a href="">X</a>
    </div>
    <!--End Update-->

    <!-- <div class="search-wrp header-select">
      <select class="select2-custom select-cycle" id="select-cycle">
        @foreach ($cycles as $cycle)
        <option value="{{ $cycle->id }}" @if( $default_question->id_cycle == $cycle->id) selected @endif>{{ $cycle->name }}</option>
        @endforeach
      </select> --><!-- Custom Select -->
      <!-- <select class="select2-custom select-category" id="select-category">
        @foreach ($question_categories as $question_category)
        <option value="{{ $question_category->id }}" @if( $default_question->id_question_categories == $question_category->id) selected @endif >{{ $question_category->name }}</option>
        @endforeach 
      </select> --><!-- Custom Select -->
      <!-- <select class="select2-custom select-question" id="select-question">
        <option value="{{ $default_question->id_question }}">{{ $default_question->question }}</option>
      </select> --><!-- Custom Select -->
    <!-- </div> -->
    <div id="map" class="map-canvas" style="position: absolute; right: 0px; top: 0px; width: 100%; height: 100%"></div>
  </section>

  <section class="filter" id="filter">
    <div class="container">
      <a href="" class="close-stickyselect"></a>
      <div class="row">
        <div class="col-md-12">
          <a class="selectarea" href="">Pilihan Wilayah</a>
        </div>
      </div>
      <div class="row">
        <div class="col-md-1">
          &nbsp;
        </div>
        <div class="col-md-11">
          <span class="custom-select-control-custom-text" style="color:white;">Sortir Hasil Survey:</span>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12 dropdown-filter">
          <ul>
          @foreach ($filters as $key_filters => $filter)
            <li>
              <select class="select-control msdd" data-maincss="blue">
                <option>{{ $filter['category_name'] }}</option>
                @foreach ($filter['category_items'] as $filter_items)
                <option value="{{ $filter_items['category_item_id'] }}">{{ $filter_items['category_item_name'] }}</option>
                @endforeach
              </select>
            </li>
            @endforeach
            <!-- Update 28112014 -->

             <li>          
              <a class="clear-all" onclick='clear_all_filter()'>{{Lang::get('frontend.clear_all')}}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>
<!--End Update-->

  <section class="survey-pemilu">
    <div class="survey-question">
      <div class="container center">
        <div class="col-xs-1 center"><a class="arrowleft" onclick="next_question(0)"></a></div>
        <p id="question-name" class="col-xs-10">" {{ $default_question->question }} "</p>
        <div class="col-xs-1 center"><a class="arrowright" onclick="next_question(1)"></a></div>
      </div>
    </div>

    <div class="container center">
      <div class="col-md-12 chart-div">
        <!-- <h3 id="survey-question">{{Lang::get('frontend.survey_question')}}</h3>
        <p id="question-name">" {{ $default_question->question }} "</p> -->
        @include('home/cross_question')
        <div class="chart chart-flag">
          <div class="notification">&nbsp;</div>
          <div class="loading-flag">
            <img src="{{ Theme::asset('img/ajax-loader.gif') }}">
          </div>
          <div id="chart_canvas">
            <div class="col-md-5"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div>
            <div class="col-md-7"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>
          </div>
          <div class="col-md-12">
            <ul class="chart-pagination">
              <li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get('frontend.preveous_question') }}</a></li>
              @if($cycles_count > 1)
              <li id="chart_pagination_text"><a class="orange-bg" onclick="compare_cycle(0)">{{Lang::get('frontend.compare_this_survey')}}</a></li>
              @endif
              <li><a class="orange-bg" onclick="next_question(1)">{{ Lang::get('frontend.next_question') }} <img src="{{ Theme::asset('img/arrow.png') }}"></a></li>
            </ul>
            <span id="filter-by-label"></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="compare-survey">
    <div class="container">
      <div class="col-md-6">
        <h4><b>{{ Lang::get('frontend.description') }}</b></h4>
        {{ Lang::get('frontend.description_content') }}
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