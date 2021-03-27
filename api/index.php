<?php
  
  /*
    Step 1:
    A simple function to respond with the correct headers
    and status code. Will convert the response into JSON.
  */
  function response ($code, $message) {
      // 获取http的状态码，并且设置新的编码状态为code
    http_response_code($code);
    //报头，content——type为浏览器返回的数据类型
    header("Content-Type: application/json");
    //对变量进行json编码，函数如果执行成功返回 JSON数据，否则返回false
    echo json_encode($message);
  }

  /*
    The connect function will be availabe to any of our
    subsequent included files. This means that to interact
    with our connection, we simply call "dbo()" anywhere.
  */
  require_once("connect.php");

  /*
    Step 2: A ReSTful API requires us to be able to understand the
    request path provided, as it will determine the key parts
    needed to build the proper response.
  */
  //var_dump(dirname($_SERVER['PHP_SELF']), $_SERVER['REQUEST_URI']);
  //注意这里self是index的所在文件夹，也就是api，request那个是request的那个link的路径
  $resolved_path = str_replace(dirname($_SERVER['PHP_SELF']), '', $_SERVER['REQUEST_URI']);

  /*
    Step 3: The "parts" are the various pieces of our requested path,
    sorted by concern.
  */
  $parts = explode("/", $resolved_path);
  $root = $parts[0];
  $resource = $parts[1];
  $action = $parts[2];
  $params = isset($parts[3]) ? array_slice($parts, 3, count($parts)) : [];

  /*
    Step 4: This is the HTTP method used to make the request
  */
  $request_method = $_SERVER['REQUEST_METHOD'];

  /*
    Step 5: The logic below first attempts to see if a resource
    has been defined (ie: "contacts").
  */
  if ($resource) {
    require_once("{$resource}/controller.php");

    switch ($action) {
      case "show":
      case "search":
        if ($request_method !== "GET") {
          return response(404, ["statusMessage" => "Not Found"]);
        }
        break;
      case "create":
      case "update":
      case "delete":
        if ($request_method !== "POST") {
          return response(404, ["statusMessage" => "Not Found"]);
        }
        break;
      default:
        if ($request_method !== "GET") {
          return response(404, ["statusMessage" => "Not Found"]);
        }

        $action = "index";
        break;
    }

    return call_user_func_array($action, $params);
  }