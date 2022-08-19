<?php


class ConfiguracoesPagamento extends ActiveRecord
{
  static $table = 'configuracoes_pagamento';

  static $before_save = ['in_store'];
}
