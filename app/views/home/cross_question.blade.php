<div class="cross-question">  
  <form class="chart-form">
    <div class='col-xs-6'>
      <div class="form-group cross-category">
        <label>{{ Lang::get('frontend.select_category') }}</label>
        <select class="cross-select-category">
        <option>{{ Lang::get('frontend.select_category') }}</option>
        @foreach ($question_categories as $question_category)
          <?php $question_category_name = $question_category->name != "" ? $question_category->name : "OTHER" ?>
          <option value="{{ $question_category->id }}">{{ $question_category_name }}</option>
        @endforeach
        </select><!-- Custom Select -->
      </div>
    </div>

    <div class='col-xs-6'>
    <div class="form-group cross-question">
      <label>{{ Lang::get('frontend.select_question') }}</label>
      <select class="cross-select-question" id="cross-select-question">
      <option>{{ Lang::get('frontend.select_question') }}</option>
      </select><!-- Custom Select -->
    </div>
    </div>

    <div class="col-md-4 cross-button-group">
      <div class="form-group">
        <button type="button" class="btn btn-primary btn-lg submit-cross">
        Submit
        </button>
      </div>
    </div>
  <form class="chart-form">

  <div class='chart bordered cross-table'>
  </div>

  <div class="alert alert-info" id="cross-alert">
    <h4>{{Lang::get('frontend.no_data')}}</h4>
  </div>

  <div class="col-md-12">
    <a href="#" class="orange-bg cross-back"><img src="{{ Theme::asset('img/arrow-l.png') }}"> {{ Lang::get('frontend.back') }}</a>
  </div>
</div>

<script type="text/html" id="get-cross-table">
<div class="col-md-12">
  <div class="table-wrapper">
    <table class="table matrix">
      <thead>
        <tr>
          <th rowspan="2"></th>
          <th id='question_header'></th>
        </tr>
        <tr id="answer_header">
        </tr>
      </thead>
      <tbody id="answer_row">
      </tbody>
    </table>
  </div>
</div>
</script>  