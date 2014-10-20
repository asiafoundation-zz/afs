<?php

class ParticipantSeeder extends Seeder {

  public function run()
  {
    $participants = array(
      array("1","1"),
      array("1","1"),
      array("1","1"),
      array("1","1"),
      array("2","1"),
      array("2","1"),
      array("2","1"),
      array("2","1"),
      array("1","2"),
      array("1","2"),
      array("1","2"),
      array("1","2"),
      array("2","2"),
      array("2","2"),
      array("2","2"),
      array("2","2"),
    );

    Participant::truncate();

    foreach ($participants as $key => $participant) {
      Participant::create(
        array(
          "region_id" => $participant[0],
          "cycle_id" => $participant[1],
        )
      );
    }
  }
}