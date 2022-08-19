<?php

class LojasPlanos extends ActiveRecord
{
  static $table = 'lojas_planos';

  static $after_save = ['in_store'];
}
