<?php

  // Step 1: index action to retrieve all the contacts
  function index () {
    try {
      $sql = "SELECT * FROM contacts";
      $result = dbo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);

      return response(200, $result);
    } catch (Exception $error) {
      return response(404, ["statusMessage" => "Issue retrieving results", "errors" => [$error->getMessage()]]);
    }
  }

  // Step 2: show action to retrieve a specific contact
  function show ($id) {
    $sql = "SELECT * FROM contacts WHERE id = :id";
    $stmt = dbo()->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) $result = json_decode("{}");
    return response(200, $result);
  }

  // Step 3: search action to retrieve contacts by a term

  /*
    "create" is a POST action that will process, validate,
    and sanitize the incoming arguments and attempt to write
    a new row to the database. The actual sanitization, validation,
    and normalization should be handled by the model, but for the
    simplicity of this lesson, we have given this to the controller.
  */
  function create () {
    try {
      $fname = filter_input(INPUT_POST, 'fname');
      $lname = filter_input(INPUT_POST, 'lname');
      $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
      $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
      $url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);

      $errors = [];
      foreach (['fname', 'lname', 'email'] as $required) {
        if (empty($$required) || !$$required) {
          $errors[] = "{$required} is required";
        }
      }

      if (count($errors) > 0) {
        return response(404, ["statusMessage" => "Issue creating contact", "errors" => $errors]);
      }

      foreach (['email', 'age', 'url'] as $required) {
        if (!empty($_POST[$required]) && !$$required) {
          $errors[] = "{$required} is not in the correct foramt";
        }
      }

      $sql = "INSERT INTO contacts (fname, lname, email, age, url) VALUES (:fname, :lname, :email, :age, :url)";
      $stmt = dbo()->prepare($sql);
      $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
      $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->bindParam(':age', $age, PDO::PARAM_INT);
      $stmt->bindParam(':url', $url, PDO::PARAM_STR);
      $stmt->execute();

      if (!$stmt) {
        throw new Exception(dbo()->errorInfo());
      }

      return response(200, ["statusMessage" => "Contact created successfully"]);
    } catch (Exception $error) {
      return response(404, ["statusMessage" => "Issue creating new contact", "errors" => [$error->getMessage()]]);
    }
  }

  /*
    "update" is a POST action that will process, validate,
    and sanitize the incoming arguments and attempt to modify
    an existing row in the database.
  */
  function update () {
    try {
      $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
      $fname = filter_input(INPUT_POST, 'fname');
      $lname = filter_input(INPUT_POST, 'lname');
      $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
      $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
      $url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);

      $errors = [];
      foreach (['id', 'fname', 'lname', 'email'] as $required) {
        if (empty($$required) || !$$required) {
          $errors[] = "{$required} is required";
        }
      }

      if (count($errors) > 0) {
        return response(404, ["statusMessage" => "Issue updating contact", "errors" => $errors]);
      }

      foreach (['email', 'age', 'url'] as $required) {
        if (!empty($_POST[$required]) && !$$required) {
          $errors[] = "{$required} is not in the correct foramt";
        }
      }

      $sql = "UPDATE contacts SET fname = :fname, lname = :lname, email = :email, age = :age, url = :url WHERE id = :id";
      $stmt = dbo()->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
      $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
      $stmt->bindParam(':email', $email, PDO::PARAM_STR);
      $stmt->bindParam(':age', $age, PDO::PARAM_INT);
      $stmt->bindParam(':url', $url, PDO::PARAM_STR);
      $stmt->execute();

      if (!$stmt) {
        throw new Exception(dbo()->errorInfo());
      }

      return response(200, ["statusMessage" => "Contact updated successfully"]);
    } catch (Exception $error) {
      return response(404, ["statusMessage" => "Issue updating contact", "errors" => [$error->getMessage()]]);
    }
  }

  /*
    "delete" is a POST action that will attempt to remove
    an existing row from the database.
  */
  function delete () {
    try {
      $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
      if (!$id) throw new Exception("Missing ID");

      $sql = "DELETE FROM contacts WHERE id = :id;"; 
      $stmt = dbo()->prepare($sql); 
      $stmt->bindParam(':id', $id); 
      $stmt->execute();

      if (!$stmt) {
        throw new Exception(dbo()->errorInfo());
      }

      return response(200, ["statusMessage" => "Contact deleted successfully"]);
    } catch (Exception $error) {
      return response(404, ["statusMessage" => "Issue deleting contact", "errors" => [$error->getMessage()]]);
    }
  }