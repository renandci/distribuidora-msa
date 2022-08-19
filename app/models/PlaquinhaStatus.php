<?php

class PlaquinhaStatus extends ActiveRecord
{

  static $table = 'plaquinha_status';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];
}
