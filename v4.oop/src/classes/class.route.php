<?php

if (!isset($magic) or !defined($magic)){
  http_response_code(404); 
  die("404 Not Found"); 
}; 


class Route
{

  private static $routes = array();
  private static $pathNotFound = null;
  private static $methodNotAllowed = null;

  public static $statusCodes = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',  // 1.1
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    509 => 'Bandwidth Limit Exceeded'
  );

  /**
   * Function used to add a new route
   * @param string $expression    Route string or expression
   * @param callable $function    Function to call if route with allowed method is found
   * @param string|array $method  Either a string of allowed method or an array with string values
   *
   */
  
  public static function add($expression, callable $function, $method = 'get')
  {
    
    array_push(self::$routes, 
          array(
            'expression' => $expression,
            'function' => $function,
            'method' => $method
          )
    );
  }

  public static function get($expression, callable $function)
  {
    self::add($expression, $function, 'get');
  }

  public static function post($expression, callable $function)
  {
    self::add($expression, $function, 'post');
  }

  public static function delete($expression, callable $function)
  {
    self::add($expression, $function, 'delete');
  }

  public static function put($expression, callable $function)
  {
    self::add($expression, $function, 'put');
  }

  public static function patch($expression, callable $function)
  {
    self::add($expression, $function, 'patch');
  }

  public static function pathNotFound(callable $function)
  {
    self::$pathNotFound = $function;
  }

  public static function methodNotAllowed(callable $function)
  {
    self::$methodNotAllowed = $function;
  }

  public static function getBody(bool $optional = true)
  {
    $body = file_get_contents('php://input');
    $json = json_decode($body);

    if (!$optional and (json_last_error() !== JSON_ERROR_NONE)) {
      throw new Exception("Bad Request - payload not in json format", 400);
    }

    return $json;
  }

  public static function run($basepath = '', $case_matters = false, $trailing_slash_matters = false, $multimatch = false)
  {

    // The basepath never needs a trailing slash
    // Because the trailing slash will be added using the route expressions
    $basepath = rtrim($basepath, '/');

    // Parse current URL
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);

    $path = '/';

    // If there is a path available
    if (isset($parsed_url['path'])) {
      // If the trailing slash matters
      if ($trailing_slash_matters) {
        $path = $parsed_url['path'];
      } else {
        // If the path is not equal to the base path (including a trailing slash)
        if ($basepath . '/' != $parsed_url['path']) {
          // Cut the trailing slash away because it does not matters
          $path = rtrim($parsed_url['path'], '/');
        } else {
          $path = $parsed_url['path'];
        }
      }
    }

    // Get current request method
    $method = $_SERVER['REQUEST_METHOD'];

    $path_match_found = false;

    $route_match_found = false;

    foreach (self::$routes as $route) {

      // havendo rota, começa a transirmar a notação simples em expressão regular:
      // primeiro substitui :i por regex de numero ([0-9]*)
      $expression = str_replace(":i", "([0-9]*)", $route['expression']);
      // segundo substitui :s por regex texto ([a-zA-Z]*)
      $expression = str_replace(":s", "([a-zA-Z]*)", $expression);
      // terceiro adiciona 
      if ($basepath != '' && $basepath != '/') {
        $expression = '(' . $basepath . ')' . $expression;
      }
     

      $expression = str_replace("/", "\/", $expression);
      $expression = str_replace(".", "\.", $expression);

      $expression = "^" . $expression . "$";

      $expression = '#' . $expression . '#' . ($case_matters ? '' : 'i');

      if (preg_match($expression, $path, $matches)) {
        $path_match_found = true;

        $params = array();

        // Cast allowed method to array if it's not one already, then run through all methods
        foreach ((array) $route['method'] as $allowedMethod) {
          // Check method match
          if (strtolower($method) == strtolower($allowedMethod)) {

            array_shift($matches); // Always remove first element. This contains the whole string

            if ($basepath != '' && $basepath != '/') {
              array_shift($matches); // Remove basepath
            }

            $params = (count($matches) > 0) ? $matches : null;
            $request = (count($_REQUEST) > 0) ? $_REQUEST : null;
            $body = self::getBody(true);

            //call_user_func_array($route['function'], $params);
            $route['function']($params, $request, $body);


            $route_match_found = true;

            // Do not check other routes
            break;
          }
        }
      }

      // Break the loop if the first found route is a match
      if ($route_match_found && !$multimatch) {
        break;
      }
    }

    // No matching route was found
    if (!$route_match_found) {
      // But a matching path exists
      if ($path_match_found) {
        if (self::$methodNotAllowed) {
          call_user_func_array(self::$methodNotAllowed, array($path, $method));
        }
      } else {
        if (self::$pathNotFound) {
          call_user_func_array(self::$pathNotFound, array($path));
        } else {
          http_response_code(404);
          $data = array("status" => "0", "message" => self::$statusCodes[404]);
          echo json_encode($data);
        }
      }
    }
  }
}
