<?php


class ConfiguracoesTransferencia extends ActiveRecord
{
  static $table = 'configuracoes_transferencia';

  static $timestamp = false;

  static $before_save = array('in_store');
}
