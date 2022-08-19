<?php

class BlogImagens extends ActiveRecord
{
  static $table = 'blog_imagens';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  static $has_one = [
    [
      'blog',
      'class_name' => 'Blog',
      'primary_key' => 'id_blog',
      'foreign_key' => 'id'
    ]
  ];
}
