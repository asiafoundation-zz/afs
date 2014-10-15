<?php

class QuestionerSeeder extends Seeder {

  public function run()
  {
    $answers = array(
      array("60","1","1","1"),
      array("20","2","1","1"),
      array("30","1","2","1"),
      array("40","2","2","1"),
      array("40","1","1","2"),
      array("30","2","1","2"),
      array("20","1","2","2"),
      array("10","2","2","2"),
      array("20","3","2","3"),
      array("10","4","2","3"),
      array("5","3","1","3"),
      array("5","4","1","3"),
      array("2","3","2","4"),
      array("4","4","2","4"),
      array("6","3","1","4"),
      array("8","4","1","4"),
    );

    Questioner::truncate();

    foreach ($answers as $key => $answer) {
      Questioner::create(
        array(
          "amount" => $answer[0],
          "answer_id" => $answer[1],
          "region_id" => $answer[2],
          "question_id" => $answer[3],
        )
      );
    }
  }
}