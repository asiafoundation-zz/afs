<div class="cross-question">  
  <form class="chart-form">
    <div class='col-xs-6'>
    <div class="form-group">
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
    <div class="form-group">
      <label>Select Question</label>
      <select class="select-control select-question">
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

  <div class='cross-table'>
  </div>

  
</div>  