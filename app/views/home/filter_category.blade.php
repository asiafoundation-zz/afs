            <ul class="dropdown-scroll">
              @foreach ($question_categories as $question_category)
              <li><a onclick='select_category({{ $question_category->id }})' id="select_category_id_{{ $question_category->id }}" >{{ $question_category->name }}</a></li>
              @endforeach
            </ul>
            <span class="arrow-down"></span>