<?php return array (
  'config' => 
  array (
    'cadastro' => 
    array (
      'tipopessoa' => 
      array (
        'status' => '1',
        'required' => '1',
      ),
      'nome' => 
      array (
        'status' => '1',
        'required' => '1',
        'text-attr' => 'Razão Social *',
        'text' => 'Nome Completo *',
        'text_required' => 'Campo nome obrigatório!',
      ),
      'email' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'E-mail (seu@email.com) *',
        'text_required' => 'Campo obrigatório ou e-mail inválido!',
      ),
      'cpfcnpj' => 
      array (
        'status' => '1',
        'required' => '1',
        'text-attr' => 'CNPJ *',
        'text' => 'CPF *',
        'text_required' => 'Campo CPF/CNPJ obrigatório!',
      ),
      'rg' => 
      array (
        'status' => '1',
        'required' => '0',
        'text-attr' => 'IE *',
        'text' => 'RG',
        'text_required' => 'Campo obrigatório!',
      ),
      'data_nascimento' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'Data Nascimento',
        'text_required' => 'Campo obrigatório!',
      ),
      'sexo' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'Sexo *',
        'text_required' => 'Qual seu sexo!',
      ),
      'telefone' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'Telefone celular ou fixo *',
        'text_required' => 'Campo obrigatório ou telefone inválido!',
      ),
      'celular' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'Celular',
        'text_required' => 'Campo obrigatório!',
      ),
      'operadora' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'Operadora',
        'text_required' => 'Qual sua operadora de celular!',
      ),
      'cidade' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'Cidade: *',
        'text_required' => 'Campo cidade é obrigatório!',
      ),
      'uf' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'UF: *',
        'text_required' => 'Campo uf é obrigatório!',
      ),
    ),
    'endereco' => 
    array (
      'configure' => 
      array (
        'status' => '0',
      ),
      'cep' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'CEP *',
        'text_required' => 'Campo CEP é obrigatório!',
      ),
      'nome' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'De um nome para seu endereço * &#38;#60;small class=&#38;#34;show mb5&#38;#34;&#38;#62;(Ex: Meu serviço, Minha casa)&#38;#60;/small&#38;#62;',
        'text_required' => 'De um nome para  seu enderço!',
      ),
      'receber' => 
      array (
        'status' => '0',
        'required' => '0',
        'text' => 'Quem irá receber *',
        'text_required' => 'Esse campo é obrigatório!',
      ),
      'endereco' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'Endereço *',
        'text_required' => 'Campo endereço é obrigatório!',
      ),
      'numero' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'Número *',
        'text_required' => 'Campo número é obrigatório!',
      ),
      'bairro' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'Bairro *',
        'text_required' => 'Campo bairro é obrigatório!',
      ),
      'complemento' => 
      array (
        'status' => '1',
        'required' => '0',
        'text' => 'Complemento',
        'text_required' => 'Campo obrigatório!',
      ),
      'referencia' => 
      array (
        'status' => '1',
        'required' => '0',
        'text' => 'Referências',
        'text_required' => 'Campo obrigatório!',
      ),
      'cidade' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'Cidade *',
        'text_required' => 'Campo cidade é obrigatório!',
      ),
      'uf' => 
      array (
        'status' => '1',
        'required' => '1',
        'text' => 'UF *',
        'text_required' => 'Campo UF é obrigatório!',
      ),
    ),
  ),
);