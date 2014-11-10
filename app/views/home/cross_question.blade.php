<div class="cross-question">  
  <form class="chart-form">
    <div class='col-xs-6'>
    <div class="form-group cross-category">
      <label>Select Category</label>
      <select class="select-control select-category">
      <option disabled>Category</option>
      @foreach ($question_categories as $question_category)
        <option value="{{ $question_category->id }}">{{ $question_category->name }}</option>
      @endforeach
      </select><!-- Custom Select -->
    </div>
    </div>

    <div class='col-xs-6'>
    <div class="form-group cross-question">
      <label>Select Question</label>
      <select class="select-control cross-select-question">
      <option>Question</option>
      @foreach ($question_lists as $question_list)
        <option value="{{ $question_list->id }}"> - {{ $question_list->question }}</option>
      @endforeach
      <option>Option Name 2</option>
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
  <div class="col-md-12">
    <a href="#" class="orange-bg cross-back"><img src="{{ Theme::asset('img/arrow-l.png') }}"> back</a>
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