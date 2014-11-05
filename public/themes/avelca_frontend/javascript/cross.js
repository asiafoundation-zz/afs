$(document).ready(function(){
  $('.cross-question').hide();
  
  $('.select-category').change(function(){
      console.log($(this).val());
    });

  $('.submit-cross').click(function(){  
    var question_row = $('.select-question').val();
    var $table = $($('#get-cross-table').html());

    $.ajax({
      type : 'post',
      url : 'cross',
      // dataType : 'array',
      data : {
        'question_header' : FilterSelect.question,
        'question_row' : 4
      },
      success : function(data){
        console.log(data['question_rows']);
        $('#question_header', $table).append(data.question_headers[0].question);
        $.each(data.question_headers, function(index, obj){
          $('#answer_header', $table).append('<th width="33.3333%">'+ obj.answer +'</th>');
        });

        
        var counter = 0;
        $.each(data['question_rows'], function(index, value){
          // console.log(data['question_rows']);
          $('#answer_row', $table).append('<tr> <td>'+ value['answer'] +'</td>');
          for(i=0;i<2;i++){
            $('#answer_row tr', $table).append('<td>'+ value['result'+i] +'</td>')
          }
          $('#answer_row', $table).append('<tr>')
        });
        
        $('.cross-table').append($table);    
      }

    })

  });  
})
