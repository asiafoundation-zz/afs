<?php

class MasterCodeSeeder extends Seeder {

  public function run()
  {
    $master_codes = array(
      array("SFL","0"),
      array("SSQ","1"),
      array("SMA","2"),
      array("SMQ","3")
    );

    MasterCode::truncate();

    foreach ($master_codes as $key => $master_code) {
      MasterCode::create(
        array(
          "master_code" => $master_code[0],
          "attribute_code" => $master_code[1]
        )
      );
    }
  }
}