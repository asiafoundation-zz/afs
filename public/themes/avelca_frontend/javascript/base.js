
    /*
     * -----------------------------------------Filter Category JS--------------------------
     */
     function find_survey()
     {
        // Get cycles functions
        $.get( "home", { region: FilterSelect.region, category: FilterSelect.category,question: FilterSelect.question, cycle: FilterSelect.cycle} )
          .done(function( data ) {
            var cycle_text = $("#cycle_select_"+cycle_id).text();
            $("#select_cycle_label").html(cycle_text);
          });
     }
     function select_question(question_id)
     {
        FilterSelect.question = question_id;
        var question_text = $("#select_question_id_"+question_id).text();
        $("#select_question_label").html(question_text);
     }
     function select_category(category_id)
     {
        FilterSelect.category = category_id;
        var category_text = $("#select_category_id_"+category_id).text();
        $("#select_category_label").html(category_text);
     }
     function cycle_select(cycle_id)
     {
        FilterSelectDefault.cycle = cycle_id;
        FilterSelect.cycle = cycle_id;

        // Get cycles functions
        $.get( "home", {category: FilterSelectDefault.category,question: FilterSelectDefault.question, cycle: FilterSelectDefault.cycle} )
          .done(function( response ) {
            var cycle_text = $("#cycle_select_"+cycle_id).text();
            $("#select_cycle_label").html(cycle_text);
            $(".survey-pemilu").html(response);
          });
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