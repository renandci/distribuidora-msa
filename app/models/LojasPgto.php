<?php

class LojasPgto extends ActiveRecord
{
  static $table = 'lojas_pgto';

  static $after_save = ['in_store'];

  static $validates_presence_of = [];

  static $validates_numericality_of = [];
}
