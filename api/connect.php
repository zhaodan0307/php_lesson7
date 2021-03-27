<?php

  /*
    By converting the connect script into a function,
    we increase its versatility and avoid the potential
    of symbol naming collisions.
  */
  function dbo () {
    try {
      $dsn = 'mysql:host=localhost;dbname=comp_1006_lesson_07';
      $username = 'root'; 
      $password = '';

      $db = new PDO($dsn,$username, $password); 

      // This attribute ensures that any SQL errors are reported
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $db;
    } catch (PDOException $error) {
        //500代表了 code有error，200代表0k，404代表不存在 后面那个
      return response(500, "Issue connecting: {$error->getMessage()}");
    }
  }