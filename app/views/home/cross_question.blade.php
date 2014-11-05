<div class="cross-question">  
  <form class="chart-form">
    <div class="form-group">
      <label>Select Category</label>
      <select class="select-control select-category">
      <option>Category</option>
      @foreach ($question_categories as $question_category)
        <option value="{{ $question_category->id }}">{{ $question_category->name }}</option>
      @endforeach
      </select><!-- Custom Select -->
    </div>
    <div class="form-group">
      <label>Select Question</label>
      <select class="select-control select-question">
      <option>Question</option>
      @foreach ($question_lists as $question_list)
        <option value="{{ $question_list->id }}">{{ $question_list->question }}</option>
      @endforeach
      <option>Option Name 2</option>
      </select><!-- Custom Select -->
    </div>
    <div class="form-group">
      <button type="button" class="btn btn-primary btn-lg submit-cross">
      Submit
      </button>
    </div>
  <form class="chart-form">

  <div class='cross-table'>
  </div>

  <div class="col-md-12">
    <a href="#" class="orange-bg"><img src="{{ Theme::asset('img/arrow-l.png') }}"> back</a>
  </div>
</div>  