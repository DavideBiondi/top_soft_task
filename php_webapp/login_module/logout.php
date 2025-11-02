<?php
session_start();

// Elimina tutte le variabili di sessione
$_SESSION = [];

// Distrugge la sessione
session_destroy();
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Logout - TopSoft</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .logout-box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    h1 {
      color: #333;
      margin-bottom: 10px;
    }
    p {
      color: #555;
      margin-bottom: 20px;
    }
    a {
      background: #0078d7;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 5px;
      transition: background 0.2s;
    }
    a:hover {
      background: #005fa3;
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h1>Logout effettuato con successo</h1>
    <p>La sessione è stata chiusa correttamente.</p>
    <p>Stai per essere reindirizzato alla pagina di login, se entro cinque secondi non la vedi, 
        clicca sul pulsante "Torna al login"</p>
    <meta http-equiv="refresh" content="5;url=login.php">
    <a href="login.php">Torna al login</a>
  </div>
</body>
</html>
