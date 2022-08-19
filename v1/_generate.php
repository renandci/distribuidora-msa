<?php

$md = Url::getURL(0);

$id = Url::getURL(1);

if (!in_array($md, [
  'cores',
  'tamanhos',
  'produtos',
  'pedidos',
  'marcas',
  'clientes',
  'enderecos',
  'opcoes',
  'fotos',
  'grupos',
  'subgrupos',
  'menus']))
  throw new Exception(sprintf('Is "%s" does not exist', $md));

$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];

$CONTENT_TYPE = $_SERVER['CONTENT_TYPE'];

$CONTENT_TYPE = explode(';', $CONTENT_TYPE);

$CONTENT_TYPE = $CONTENT_TYPE[0];

if (
  $CONTENT_TYPE != 'application/json' &&
  $CONTENT_TYPE != 'multipart/form-data'
)
  throw new Exception(sprintf('Is %s does not exist', $CONTENT_TYPE));

$REQUEST_CONTROLLER = 'Controller' . ucfirst($md);

switch ($REQUEST_METHOD) {
    // Somente para editar os dados
  case 'DELETE':
    if (!strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE')) {
      // parse_str(file_get_contents('php://input'), $_DELETE);
      $_DELETE = json_decode(file_get_contents("php://input"), true);
    }

    $ReturnController = $REQUEST_CONTROLLER::delete();
    print(json_encode($ReturnController));
    break;

    // Somente para editar os dados
  case 'PUT':
    if (!strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT'))
      $_PUT = json_decode(file_get_contents("php://input"), true);
    // parse_str(file_get_contents('php://input'), $_PUT);

    $ReturnController = $REQUEST_CONTROLLER::create_or_edit();
    print(json_encode($ReturnController));
    break;

    // Somente para cadastro
  case 'POST':
    if (!strcasecmp($_SERVER['REQUEST_METHOD'], 'POST'))
      if ($CONTENT_TYPE == 'application/json')
        $_POST = json_decode(file_get_contents('php://input'), true);

    $ReturnController = $REQUEST_CONTROLLER::create_or_edit();
    print(json_encode($ReturnController));
    break;

    // Padrao será GET
  default:
    if (!strcasecmp($_SERVER['REQUEST_METHOD'], 'GET')) {
      // parse_str(file_get_contents('php://input'), $_GET);
      $_GET = json_decode(file_get_contents("php://input"), true);
    }
    $ReturnController = $REQUEST_CONTROLLER::find_by_all((int)$id);
    print(json_encode($ReturnController, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    break;
}
