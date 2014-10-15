<?php

class SurveySeeder extends Seeder {

  public function run()
  {
    $surveys = array(
      array("Presiden dan Wakil Presiden Pilihan Anda","","","")
    );

    Survey::truncate();

    foreach ($surveys as $key => $survey) {
      Survey::create(
        array(
          "name" => $survey[0],
          "geojson_file" => $survey[1],
          "baseline_file" => $survey[2],
          "endline_file" => $survey[3],
        )
      );
    }
  }
}