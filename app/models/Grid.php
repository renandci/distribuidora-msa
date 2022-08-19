<?php

class Grid extends ActiveRecord
{
  static $table = 'grid';

  static $before_save = ['in_store'];

  static $has_one = [
    [
      'grid',
      'class_name' => 'Grid',
      'primary_key' => 'parent_id',
      'foreign_key' => 'id', [
        'grid',
        'through' => 'grid'
      ]
    ], [
      'grid_name',
      'class_name' => 'Grid',
      'primary_key' => 'parent_id',
      'foreign_key' => 'id',
      'order' => 'grid_ordem desc'
    ]
  ];


  static $belongs_to = [
    [
      'grid_prent',
      'class_name' => 'Grid',
      'primary_key' => 'parent_id',
      'foreign_key' => 'id',
      'order' => 'grid_ordem desc'
    ]
  ];

  static $has_many = [
    [
      'grid_children',
      'class_name' => 'Grid',
      'primary_key' => 'id',
      'foreign_key' => 'parent_id',
      'order' => 'grid_ordem desc'
    ]
  ];
}
