<?php

class CorreiosServicos extends ActiveRecord
{
  static $table = 'correios_servicos';

  static $after_save = ['in_store'];
}
