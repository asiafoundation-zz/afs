$(document).ready(function(){
  $('.cross-question').hide();
  $('.chart-flag').show();
  

  $('.show-cross').click(function(e){
    e.preventDefault();

    $('.cross-question').show();
    $('.chart-flag').hide();
  })

  $('.select-category').change(function(){
      console.log($(this).val());
  });

  $('.select-question').change(function(){
      console.log($(this).val());

      $('.submit-cross').data('question_id', $(this).val());
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
    var $table = $($('#get-cross-table').html());

    $('.cross-table').html("");

    $.ajax({
      type : 'post',
      url : 'cross',
      data : {
        'question_header' : FilterSelect.question,
        'question_row' : question_row
      },
      success : function(data){
        var count_value = 0;
        

        $('#question_header', $table).append(data.question_headers[0]['question']);
        $.each(data.question_headers, function(index, value){
          $('#answer_header', $table).append('<th>'+ value['answer'] +'</th>');
          count_value++;
        });

        $('#question_header', $table).attr('colspan',count_value);

        $.each(data['question_rows'], function(index, value){
          result = '<tr><td width="20%">'+ value['answer'] +'</td>';
          for(i=0;i<count_value;i++){
            result += '<td align="center">'+ value['result'+i] +'</td>';
          }
          result += '</tr>';

          $('#answer_row', $table).append(result);

        });
        
        $('.cross-table').append($table);    
      }

    })

  }); 

})
