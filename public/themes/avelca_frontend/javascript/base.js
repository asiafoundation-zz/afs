    /*
     * -----------------------------------------Filter Category JS--------------------------
     */
     function select_question(question_id)
     {
        var question_text = $("#select_question_id_"+question_id).text();
        FilterSelect.question = question_text;
        $("#select_question_label").html(question_text);
     }
     function select_category(category_id)
     {
        FilterSelect.category = category_id;
        var category_text = $("#select_category_id_"+category_id).text();
        $("#select_category_label").html(category_text);
     }
     function clear_all_filter()
     {
      window.location.reload();
     }
    /*
     * -----------------------------------------END Filter Category  JS--------------------------
     */

    $('.search-wrp > div > a').click(function(){
      $(this).siblings('.dropdown-path').show();
      return false;
    })

    $('body').click(function(){
      $('.dropdown-path').hide();
    })

    $('.search-wrp > div > a#question').click(function(){
      $('.search-wrp > div > a#category + .dropdown-path').hide();
    })

    $('.search-wrp > div > a#category').click(function(){
      $('.search-wrp > div > a#question + .dropdown-path').hide();
    })

    $('.dropdown-scroll').alternateScroll({ 'vertical-bar-class': 'styled-v-bar', 'hide-bars': false });