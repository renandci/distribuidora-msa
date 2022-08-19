<?php

class NfeCidades extends ActiveRecord
{
  static $table = 'nfe_cidades';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];
}
