<?php

class NfeNcm extends ActiveRecord
{
  static $table = 'nfe_ncm';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];
}
