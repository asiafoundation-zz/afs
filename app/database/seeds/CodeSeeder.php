<?php

class CodeSeeder extends Seeder {

  public function run()
  {
    $codes = array(
      array("REGION","1"),
      array("AGE","1"),
      array("STUDY","1"),
      array("SES","1"),
      array("AREA","1"),
      array("GENDER","1"),
    );

    Code::truncate();

    foreach ($codes as $key => $code) {
      Code::create(
        array(
          "code" => $code[0],
          "master_code_id" => $code[1],
        )
      );
    }
  }
}