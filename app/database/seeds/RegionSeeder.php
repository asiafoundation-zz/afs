<?php

class RegionSeeder extends Seeder {

  public function run()
  {
    $answers = array(
      array("Kalimantan Timur","1"),
      array("Kalimantan Utara","1"),
      array("Jawa Barat","1"),
      array("Jambi","1"),
      array("Gorontalo","1"),
      array("Daerah Khusus Ibukota Jakarta","1"),
      array("Daerah Isimewa Yogyakarta","1"),
      array("Bengkulu","1"),
      array("Banten","1"),
      array("Bali","1"),
      array("Aceh","1"),
      array("Jawa Tengah","1"),
      array("Jawa Timur","1"),
      array("Kalimantan Barat","1"),
      array("Kalimantan Selatan","1"),
      array("Kalimantan Tengah","1"),
      array("Kepulauan Bangka Belitung","1"),
      array("Kepulauan Riau","1"),
      array("Lampung","1"),
      array("Maluku","1"),
      array("Maluku Utara","1"),
      array("Nusa Tenggara Barat","1"),
      array("Nusa Tenggara Timur","1"),
      array("Papua","1"),
      array("Papua Barat","1"),
      array("Riau","1"),
      array("Sulawesi Barat","1"),
      array("Sulawesi Selatan","1"),
      array("Sulawesi Tengah","1"),
      array("Sulawesi Tenggara","1"),
      array("Sulawesi Utara","1"),
      array("Sumatera Barat","1"),
      array("Sumatera Selatan","1"),
      array("Sumatera Utara","1"),
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