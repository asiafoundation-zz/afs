<?php

class RegionSeeder extends Seeder {

  public function run()
  {
    $answers = array(
      array("Kalimantan Timur","1"),
      array("Kalimantan Utara","1")
    );

    Region::truncate();

    foreach ($answers as $key => $answer) {
      Region::create(
        array(
          "name" => $answer[0],
          "code_id" => $answer[1],
        )
      );
    }
  }
}