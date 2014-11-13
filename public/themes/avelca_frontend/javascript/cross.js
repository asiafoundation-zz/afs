$(document).ready(function(){

  /*Define select2*/
  $(function(){
      $('.select-cycle, .select-category, .select-question, .cross-select-category, .cross-select-question').select2({});
  });
  
  $('.cross-question').hide();
  $('.chart-flag').show();
  

  $('.show-cross').click(function(e){
    e.preventDefault();

    $('.cross-question').show();
    $('.chart-flag').hide();
  });

  /*$('.select-category').change(function(){
      
  });*/

  $('.cross-select-question').change(function(){
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

      }

    });
  }); 

  $('.cross-back').click(function(e){
    e.preventDefault();
    
    $('.cross-question').hide().css('display', 'none').fadeOut('slow');
    $('.chart-flag').show();
    find_survey();
  });

  $('.select-question').change(function(e){
    FilterSelect.question = parseInt($(this).val());
    find_survey();
  });

  $('.select-cycle').change(function(){
    var id_cycle = $(this).val();
    FilterSelect.cycle = parseInt(id_cycle);

    cycle_select(id_cycle);
  });
})
