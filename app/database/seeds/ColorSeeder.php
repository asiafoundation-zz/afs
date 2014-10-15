<?php

class ColorSeeder extends Seeder {

  public function run()
  {
    $colors = array(
      array("#ED3749"),
      array("#F7CD23"),
      array("#000"),
      array("#0E35CF")
    );

    Color::truncate();

    foreach ($colors as $key => $color) {
      Color::create(
        array(
          "color" => $color[0]
        )
      );
    }
  }
}