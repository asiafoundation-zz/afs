$(document).ready(function(){
  $('.cross-question').hide();
  $('.chart-flag').show();
  

  $('.show-cross').click(function(e){
    e.preventDefault();

    $('.cross-question').show();
    $('.chart-flag').hide();
  })

  /*$('.select-category').change(function(){
      
  });*/

  $('.select-question').change(function(){
      console.log($(this).val());

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
    var $table = $($('#get-cross-table').html()); //inisiate js template

    $('.cross-table').html("");

    $.ajax({
      type : 'post',
      url : 'cross',
      data : {
        'question_header' : FilterSelect.question,
        'question_row' : question_row
      },
      success : function(data){
        var count_value = 0; //inisiate variable for question header count

        //show question header
        $('#question_header', $table).append(data.question_headers[0]['question']);
        $.each(data.question_headers, function(index, value){
          $('#answer_header', $table).append('<th>'+ value['answer'] +'</th>');
          count_value++;
        });

        $('#question_header', $table).attr('colspan',count_value);

        //show question row
        $.each(data['question_rows'], function(index, value){
          result = '<tr><td width="20%">'+ value['answer'] +'</td>'; //create html for showing question row data
          for(i=0;i<count_value;i++){
            result += '<td align="center">'+ value['result'+i] +'</td>';
          }
          result += '</tr>';

          $('#answer_row', $table).append(result); //append html to tempate

        });
        
        $('.cross-table').append($table); //append template to cross-table class
      }

    })

  }); 

})
