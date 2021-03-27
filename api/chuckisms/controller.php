<?php

  // Step 1: The index action to retrieve a random chuck quote
  function index () {
    $response = file_get_contents("https://api.chucknorris.io/jokes/random");
    //json_decode接受一个json编码字符串然后把他变成了php字符串变量
    return response(200, json_decode($response));
  }