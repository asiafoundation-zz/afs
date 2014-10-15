<?php

class CategoryItemSeeder extends Seeder {

  public function run()
  {
    $category_items = array(
      array("AGE","2"),
      array("EDUCATION","3")
    );

    CategoryItem::truncate();

    foreach ($category_items as $key => $category_item) {
      CategoryItem::create(
        array(
          "name" => $category_item[0],
          "category_id" => $category_item[1]
        )
      );
    }
  }
}