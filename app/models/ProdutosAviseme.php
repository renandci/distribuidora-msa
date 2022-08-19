<?php

class ProdutosAviseMe extends ActiveRecord
{
  static $table = 'produtos_aviseme';

  static $before_save = array('in_store');
}
