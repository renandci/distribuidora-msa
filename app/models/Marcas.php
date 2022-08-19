<?php

class Marcas extends ActiveRecord
{
  static $table = 'marcas';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];
}
