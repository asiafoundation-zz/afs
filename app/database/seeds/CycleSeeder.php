<?php

class CycleSeeder extends Seeder {

  public function run()
  {
    $cycles = array(
      array("Juni","0"),
      array("Juli","1")
    );

    Cycle::truncate();

    foreach ($cycles as $key => $cycle) {
      Cycle::create(
        array(
          "name" => $cycle[0],
          "cycle_type" => $cycle[1]
        )
      );
    }
  }
}