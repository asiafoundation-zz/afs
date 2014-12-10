$(document).ready(function(){
  var default_q = parseInt(FilterSelect.default_question);
  var default_cat = parseInt(FilterSelect.default_category);
  var default_cy = parseInt(FilterSelect.default_cycle);
  $('.cross-question #cross-alert').hide();
  /*Define select2*/
  $(function(){
      $('.select-cycle, .select-category, .select-question, .cross-select-category, .cross-select-question').select2({});
  });
  
  $('.cross-question').hide();
  $('.chart-flag').show();
  

  $('.show-cross').click(function(e){
    e.preventDefault();

    // $('html, body').animate({scrollTop: $(".survey-question").offset().top}, 1000);
    $('.cross-question').show();
    $('.chart-flag').hide();
  });

  $('.cross-select-question').change(function(){
      $('.submit-cross').data('question_id', $(this).val()); //send data-question_id to button
  });

  function numAttrs(obj) {
    var count = 0;
    for(var key in obj) {
      if (obj.hasOwnProperty(key)) {
        ++count;
      }
    }
    return count;
  }

  $('.submit-cross').click(function(){  
    var question_row = $(this).data('question_id');

    $('.cross-table').html("");

    $.ajax({
      type : 'post',
      url : 'cross',
      data : {
        'question_header' : FilterSelect.question,
        'question_row' : question_row
      },
      success : function(data){
        var count_array = Object.keys(data.question_headers).length;
        
        if(Object.keys(data.question_rows[1]).length != 0){
          $('.cross-question #cross-alert').hide();
          
          for(var a=0;a<count_array;a++){
            var count_value = 0; //inisiate variable for question header count
            
            //show question header  
            var $table = $($('#get-cross-table').html().trim()); //inisiate js template
            $('#question_header', $table).append(data.question_headers[a][0]['question']);
            $.each(data.question_headers[a], function(index, value){
              $('#answer_header', $table).append('<th>'+ value['answer'] +'</th>');
              count_value++;
            });

            $('#question_header', $table).attr('colspan',count_value);

            //show question row
            $.each(data.question_rows[a], function(index, value){
              result = '<tr><td width="20%">'+ value['answer'] +'</td>'; //create html for showing question row data
              for(i=0;i<count_value;i++){
                result += '<td align="center">'+ value['result'+i] +'</td>';
              }
              result += '</tr>';

              $('#answer_row', $table).append(result); //append html to tempate

            });
            
            $('.cross-table').append($table); //append template to cross-table class
          }  
        }else{
          $('.cross-question #cross-alert').show();
        }
      }

    });
  }); 

  $('.cross-back').click(function(e){
    e.preventDefault();
    
    // $('html, body').animate({scrollTop: $(".survey-question").offset().top}, 1000);
    $('.cross-question').hide().css('display', 'none').fadeOut('slow');
    $('.chart-flag').show();
    find_survey();
  });

  $('.select-category').change(function(){
    
    var value = $(this).val();
    $.get( "filter-select", { SelectedFilter:"loadcategory", category: $(this).val(), cycle : FilterSelect.cycle} )
    .done(function(data){
      // var data_exist = parseInt(data[1]);

      $('.header-select #select-question option').remove();
      // $('.header-select #select-question').append($("<option></option>").attr("value","0").text(" "))
      $.each(data[0], function(index, obj){
        $('.header-select #select-question').append($("<option></option>").attr("value",obj.id).text(obj.question))
      });

    });
  });

  $('.cross-select-category').change(function(){
    var value = $(this).val();
    $.get( "filter-select", { SelectedFilter:"loadcategory", category: $(this).val(), cycle : FilterSelect.cycle} )
    .done(function(data){
      $('#cross-select-question option').remove()
      $.each(data[0], function(index, obj){
        $('#cross-select-question').append($("<option></option>").attr("value",obj.id).text(obj.question))
      });
    });
  });

  $('#select-question').change(function(e){
    // $('html, body').animate({scrollTop: $(".survey-question").offset().top}, 1000);
    // $('.header-select #select-question option[value="0"]').remove();
    if(FilterSelect.empty_question == 0){
      FilterSelect.empty_question = 0;
    }
    if($(this).val() == 0){
      FilterSelect.question = default_q;
      FilterSelect.category = default_cat;
      FilterSelect.cycle = default_cy;
      find_survey();
    }else{
      FilterSelect.question = parseInt($(this).val());
      FilterSelect.category = parseInt($('#select-category').val());
      FilterSelect.cycle = parseInt($('#select-cycle').val());

      find_survey();  
    }
    
  });

  $('.select-cycle').change(function(){
    
  });

  // $('#select-region').change(function(){
  //   if($(this).val() != null){
  //     FilterSelect.region = parseInt($(this).val());
  //     find_survey_dynamic();
  //   }else{
  //     FilterSelect.region = "";
  //     find_survey();
  //   }
  // });

  $('.clear-all').click(function(){
    clear_filter();    
  })
})

function clear_filter(){
  FilterSelect.region = "";
  option_filters_default = [];
  filter_option(0);
  clear_all_filter_nosurvey();
  find_survey();

  disable_anchor($(this), 0);
}