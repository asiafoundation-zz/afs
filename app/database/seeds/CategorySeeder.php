<?php

class CategorySeeder extends Seeder {

  public function run()
  {
    $categories = array(
      array("AGE","2"),
      array("EDUCATION","3")
    );

    Category::truncate();

    foreach ($categories as $key => $category) {
      Category::create(
        array(
          "name" => $category[0],
          "code_id" => $category[1]
        )
      );
    }
  }
}