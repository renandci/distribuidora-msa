<?php

class Blog extends ActiveRecord
{
  static $table = 'blog';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  static $has_many = [
    [
      'blogimg',
      'class_name' => 'BlogImagens',
      'foreign_key' => 'id_blog',
      'primary_key' => 'id',
    ], [
      'blogimgrand',
      'class_name' => 'BlogImagens',
      'foreign_key' => 'id_blog',
      'primary_key' => 'id',
      'order' => 'rand()'
    ]
  ];
}
