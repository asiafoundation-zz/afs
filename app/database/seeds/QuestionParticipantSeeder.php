<?php

class QuestionParticipantSeeder extends Seeder {

  public function run()
  {
    $participants = array(
      array("1","1"),
      array("2","1"),
      array("3","2"),
      array("4","2"),
      array("5","1"),
      array("6","1"),
      array("7","2"),
      array("8","1"),

      array("9","1"),
      array("10","2"),
      array("11","2"),
      array("12","1"),
      array("13","2"),
      array("14","1"),
      array("15","1"),
      array("16","1"),
    );

    QuestionParticipant::truncate();

    foreach ($participants as $key => $participant) {
      QuestionParticipant::create(
        array(
          "participant_id" => $participant[0],
          "answer_id" => $participant[1],
        )
      );
    }
  }
}