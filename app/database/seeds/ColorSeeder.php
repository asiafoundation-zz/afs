<?php

class ColorSeeder extends Seeder {

  public function run()
  {
    $colors = array(
      array("#FA0C0C"),
      array("#E600FF"),
      array("#6200FF"),
      array("#00B3FF"),
      array("#00FF1E"),
      array("#CCFF00"),
      array("#FFAE00"),
      array("#FF470A"),
      array("#F5ABAB"),
      array("#996D6D"),
      array("#961515"),
      array("#E785F2"),
      array("#85588A"),
      array("#7E018C"),      
      array("#B68DF7"),
      array("#67597D"),
      array("#350D75"),      
      array("#99DBF7"),
      array("#4D6B78"),
      array("#025173"),
      array("#9CF7A7"),
      array("#47734C"),
      array("#036E10"),
      array("#E1F788"),
      array("#759103"),
      array("#848F59"),
      array("#FAD991"),
      array("#8F6203"),
      array("#91815D"),
      array("#F7A488"),
      array("#825648"),
      array("#85290B"),
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