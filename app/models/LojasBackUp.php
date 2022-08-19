<?php

class LojasBackUp extends ActiveRecord
{
  static $table = 'lojas_backup';

  static $after_save = ['in_store'];

  static $validates_presence_of = [];

  static $validates_numericality_of = [];
}
