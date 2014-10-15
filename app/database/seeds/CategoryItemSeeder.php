<?php

class CategoryItemSeeder extends Seeder {

  public function run()
  {
    $category_items = array(
      array("0-10","1"),
      array("10-20","1"),
      array("20-30","1"),
      array("SD","2"),
      array("SMP","2"),
      array("SMA","2"),
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