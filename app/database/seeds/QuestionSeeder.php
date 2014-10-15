<?php

class QuestionSeeder extends Seeder {

  public function run()
  {
    $questions = array(
      array("0","Siapakah Calon Presiden Pilihan Anda","1","1","1"),
      array("0","Siapakah Calon Presiden Pilihan Anda","1","2","0"),
      array("1","Siapakah Calon Wakil Presiden Pilihan Anda","1","1","0"),
      array("1","Siapakah Calon Wakil Presiden Pilihan Anda","1","2","0"),
    );

    Question::truncate();

    foreach ($questions as $key => $question) {
      Question::create(
        array(
          "code" => $question[0],
          "question" => $question[1],
          "question_category_id" => $question[2],
          "cycle_id" => $question[3],
          "is_default" => $question[4],
        )
      );
    }
  }
}