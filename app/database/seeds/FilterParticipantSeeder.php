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