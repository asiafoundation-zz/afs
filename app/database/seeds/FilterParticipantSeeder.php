<?php

class FilterParticipantSeeder extends Seeder {

  public function run()
  {
    $participants = array(
      array("1","1"),
      array("4","1"),
      array("2","2"),
      array("5","2"),
      array("3","3"),
      array("6","3"),
      array("1","4"),
      array("4","4"),
      array("1","5"),
      array("3","6"),
      array("2","7"),
      array("6","8"),

      array("1","9"),
      array("4","9"),
      array("2","10"),
      array("5","10"),
      array("3","11"),
      array("6","11"),
      array("1","12"),
      array("4","12"),
      array("2","13"),
      array("5","14"),
      array("3","15"),
      array("6","16"),
    );

    FilterParticipant::truncate();

    foreach ($participants as $key => $participant) {
      FilterParticipant::create(
        array(
          "category_item_id" => $participant[0],
          "participant_id" => $participant[1],
        )
      );
    }
  }
}