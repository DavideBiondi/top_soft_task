<?php session_start(); ?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Login - TopSoft</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    form {
      background: #fff;
      padding: 20px 30px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.2);
    }
    input[type=text], input[type=password] {
      width: 100%;
      padding: 8px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    input[type=submit] {
      background: #0078d7;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 4px;
      cursor: pointer;
    }
    input[type=submit]:hover {
      background: #005fa3;
    }
  </style>
</head>
<body>

<form method="post" action="authenticate.php">
  <h2>Login Amministratore</h2>
  <label>Username:</label><br>
  <input type="text" name="username" required><br>
  <label>Password:</label><br>
  <input type="password" name="password" required><br>
  <input type="submit" value="Accedi">
  <?php
    if (isset($_SESSION['error'])) {
      echo "<p style='color:red;'>".$_SESSION['error']."</p>";
      unset($_SESSION['error']);
    }
  ?>
</form>

</body>
</html>
