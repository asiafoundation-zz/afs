@extends('layouts/default')

@section('content')

  @include('partial/homeasset')

<?php 
  $survey_select = Survey::all();  
?>
<section class="header">
    <!-- <div class="flag">
      {{ Form::open(['action' => 'HomeController@postLang'] ) }}
      <span>language : </span>
      <label>
        <select onchange="this.form.submit()" name="lang">
          @foreach($survey_select as $row)
            <option value="{{ $row->id }}" @if($survey->id == $row->id) selected @endif>{{ $row->url_name }}</option>
          @endforeach
        </select>
      </label>
      {{ Form::close()}}
    </div> -->
    <p>{{ $survey->name }}</p>
</section>

<!--Update-->
<div class="elheader-wrapper"> <!-- add element wrapper - 28112014 -->
  <div class="search-wrp header-select">
    <div class="select-1">
      <label>1) {{ Lang::get('frontend.choose_survey') }}</label>
      <select class="select2-custom select-cycle" id="select-cycle">
        @foreach ($cycles as $cycle)
        <option value="{{ $cycle->id }}" @if( $default_question->id_cycle == $cycle->id) selected @endif>@if($cycle->display_name != "") {{ $cycle->display_name }} @else {{ $cycle->name }} @endif</option>
        @endforeach
      </select><!-- Custom Select -->
    </div>
    <div class="select-2">
      <label>2) {{ Lang::get('frontend.choose_category') }}</label>
      <select class="select2-custom select-category" id="select-category">
        @foreach ($question_categories as $question_category)
        <option value="{{ $question_category->id }}" @if( $default_question->id_question_categories == $question_category->id) selected @endif >{{ $question_category->name }}</option>
        @endforeach 
      </select><!-- Custom Select -->
    </div>
    <div class="select-3">
      <label>3) {{ Lang::get('frontend.choose_question') }}</label>
      <select class="select2-custom select-question" id="select-question">
        @foreach ($question_lists as $question_list)
        <option value="{{ $question_list->id }}" @if( $default_question->id_question == $question_list->id) selected @endif >{{ $question_list->question }}</option>
        @endforeach 
      </select><!-- Custom Select -->
    </div>
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

    <div id="map" class="map-canvas" style="position: absolute; right: 0px; top: 0px; width: 100%; height: 100%"></div>
  </section>
  <section class="survey-pemilu">
    <div class="survey-question">
      <label>PERTANYAAN SURVEY</label>
      <div class="container center">
        <div class="col-xs-1 center"><a class="arrowleft" onclick="next_question(0)"></a></div>
        <p id="question-name" class="col-xs-10">" {{ $default_question->question }} "</p>
        <div class="col-xs-1 center"><a class="arrowright" onclick="next_question(1)"></a></div>
      </div>
    </div>
      <section class="filter" id="filter">
        <div class="container">
         <div class="row">
            <div class="col-md-11">
               <span class="custom-select-control-custom-text" style="color:white;">Saring Hasil Survey:</span>
            </div>
         </div>
        <div class="row">
          <div class="col-md-12 dropdown-filter">
            <ul>
            <li class="li-region">
               <select class="select-control" id="select-region">
                  <option class="selectik-filter-region">Wilayah</option>
                  @foreach ($regions as $region)
                  <option value="{{ $region['region_id'] }}" class="selectik-filter-region">{{ $region['name'] }}</option>
                  @endforeach
               </select>
            </li>
            @foreach ($filters as $key_filters => $filter)
              <li class="li-filter">
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
                <a class="clear-all" onclick="clear_filter()">{{Lang::get('frontend.clear_all')}}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  <!--End Update-->

    <div class="container center">
      <div class="col-md-12 chart-div">
        @include('home/cross_question')
        <div class="chart chart-flag">
          <div class="notification">&nbsp;</div>
          <div class="loading-flag">
            <img src="{{ Theme::asset('img/ajax-loader.gif') }}">
          </div>
          <div id="chart_canvas">
            <div class="col-md-5" id="pie-div"><div id="chartContainerPie" style="height: 300px; width: 100%;"></div></div>
            <div class="col-md-7" id="chart-div"><div id="chartContainer" style="height: 300px; width: 100%;"></div></div>
          </div>
          <div class="col-md-12">
            <ul class="chart-pagination">
              <li><a class="orange-bg" onclick="next_question(0)"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get('frontend.preveous_question') }}</a></li>
              @if($cycles_count > 1)
              <li id="chart_pagination_text">
                <a class="orange-bg" onclick="compare_cycle(0)">{{Lang::get('frontend.compare_this_survey')}}</a>
              </li>
              @endif
              
            </ul>
            <span id="filter-by-label">{{ Lang::get('frontend.all_survey') }}</span><span id="region-label"></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="compare-survey">
    <div class="container">
      <div class="col-md-6">
        <h4><b>{{ Lang::get('frontend.description') }}</b></h4>
        {{ $survey->information }}
      </div>
      <div class="col-md-6">
        <div class="extras">
          <img src="{{ Theme::asset('img/compare.png') }}">
          <div>
            <h4>{{Lang::get('frontend.compare_survey_results')}}</h4>
            <p>{{ Lang::get('frontend.compare_survey_results_content') }}</p>
            <a onclick='compare_cycle(0)' class="orange-bg">{{Lang::get('frontend.compare_survey')}}</a>
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