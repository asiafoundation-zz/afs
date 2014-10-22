<?php

class AnswerSeeder extends Seeder {

  public function run()
  {
    $answers = array(
      array("JOKOWI","1","1","1"),
      array("PRABOWO","1","2","1"),
      array("JOKOWI","1","1","2"),
      array("PRABOWO","1","2","2"),
      array("JUSUF KALLA","2","1","1"),
      array("HATTA RAJASA","2","2","1"),
      array("JUSUF KALLA","2","1","2"),
      array("HATTA RAJASA","2","2","2"),
      array("DIN SYAMSUDIN","2","3","1"),
      array("AMIEN RAIS","2","4","1"),
      array("DIN SYAMSUDIN","2","3","2"),
      array("AMIEN RAIS","2","4","2"),
    );

      // array("JOKOWI","1","1"),
      // array("PRABOWO","1","2"),
      // array("JOKOWI","2","1"),
      // array("PRABOWO","2","2"),
      // array("JUSUF KALLA","3","1"),
      // array("HATTA RAJASA","3","2"),
      // array("JUSUF KALLA","4","1"),
      // array("HATTA RAJASA","4","2"),
      // array("DIN SYAMSUDIN","3","3"),
      // array("AMIEN RAIS","3","4"),
      // array("DIN SYAMSUDIN","4","3"),
      // array("AMIEN RAIS","4","4"),
      
    Answer::truncate();

    foreach ($answers as $key => $answer) {
      Answer::create(
        array(
          "answer" => $answer[0],
          "question_id" => $answer[1],
          "color_id" => $answer[2],
          "cycle_id" => $answer[3],
        )
      );
    }
  }
}