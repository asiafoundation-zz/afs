<?php

class QuestionCategorySeeder extends Seeder {

  public function run()
  {
    $question_categories = array(
      array("Default Questions","1")
    );

    QuestionCategory::truncate();

    foreach ($question_categories as $key => $question_category) {
      QuestionCategory::create(
        array(
          "name" => $question_category[0],
          "survey_id" => $question_category[1]
        )
      );
    }
  }
}