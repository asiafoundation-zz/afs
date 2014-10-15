<?php

class AnswerSeeder extends Seeder {

  public function run()
  {
    $answers = array(
      array("JOKOWI","1","1"),
      array("PRABOWO","1","2"),
      array("JUSUF KALLA","2","1"),
      array("HATTA RAJASA","2","2")
    );

    Answer::truncate();

    foreach ($answers as $key => $answer) {
      Answer::create(
        array(
          "answer" => $answer[0],
          "question_id" => $answer[1],
          "color_id" => $answer[2],
        )
      );
    }
  }
}