<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    # code...
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="IT">
<head>
  <style>
    .hidden{display: none;}
    .visible{display: block;}
  </style>
    <meta charset="UTF-8">
    <title>Dashboard - TopSoft</title>
</head>
<body>
<?php
if (isset($_SESSION['success'])) {
    echo "<p style='color: green; font-weight: bold;'>" . htmlspecialchars($_SESSION['success']) . "</p>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo "<p style='color: red; font-weight: bold;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
?>

    <h1>Benvenuto, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <div>
      <button id="btn_ricerca">Ricerca Cliente</button>
      <button id="btn_crea">Crea Cliente</button>
      <button id="btn_modifica">Modifica Cliente</button>
      <button id="btn_elimina">Elimina Cliente</button>

    </div>
    <!-- Form di Ricerca -->
    <form method="post" class="visible" id="ricerca_cliente" action="search.php">
  <h2>Ricerca Cliente</h2>
  <label>Nome</label><br>
  <input type="text" name="nome"><br>
  <label>Cognome</label><br>
  <input type="text" name="cognome"><br>
  <label>Telefono</label><br>
  <input type="text" name="numero_telefono"><br>
  <label>Email</label><br>
  <input type="email" name="email"><br>
  <label>P.IVA</label><br>
  <input type="text" name="numero_piva"><br>
  <label>Data di inserimento (formato yyyy-mm-gg)</label><br>
  <input type="text" name="data_inserimento" 
  pattern="(?<=(^19\d\d|^20\d\d)-02-)(0[1-9]|2[0-9])$|^(19\d\d|20\d\d)-(0[469]|11)-(0[1-9]|1[1-9]|2[1-9]|30)$|^(19\d\d|20\d\d)-(0[13578]|12)-(0[1-9]|1[1-9]|2[1-9]|3[01])$">
  <br>
  <label>Codice ATECO</label><br>
  <input type="text" name="codice_ateco"><br>
  <label>Nome dell'azienda</label><br>
  <input type="text" name="nome_azienda"><br>
  <label>Nome del gruppo</label><br>
  <input type="text" name="nome_gruppo"><br><br>
  <input type="submit" value="Ricerca">
  <?php
    if (isset($_SESSION['error'])) {
      echo "<p style='color:red;'>".$_SESSION['error']."</p>";
      unset($_SESSION['error']);
    }
  ?>
</form>
    <!-- Form di Creazione cliente -->
    <form method="post" class="hidden" id="crea_cliente" action="create.php">
  <h2>Crea Cliente</h2>
  <label>Nome*</label><br>
  <input type="text" name="nome" required><br>
  <label>Cognome*</label><br>
  <input type="text" name="cognome" required><br>
  <label>Telefono</label><br>
  <input type="text" name="numero_telefono"><br>
  <label>Email*</label><br>
  <input type="email" name="email" required><br>
  <label>P.IVA*</label><br>
  <input type="text" name="numero_piva" required><br>
  <label>Nome dell'azienda*</label><br>
  <input type="text" name="nome_azienda" required><br>
  
  <label>Data di attivazione (formato yyyy-mm-gg)</label><br>
  <input type="text" name="data_attivazione" 
  pattern="(?<=(^19\d\d|^20\d\d)-02-)(0[1-9]|2[0-9])$|^(19\d\d|20\d\d)-(0[469]|11)-(0[1-9]|1[1-9]|2[1-9]|30)$|^(19\d\d|20\d\d)-(0[13578]|12)-(0[1-9]|1[1-9]|2[1-9]|3[01])$">
  <br>
  <label>Denominazione</label><br>
  <input type="text" name="denominazione" maxlength="4"><br>
  <label>Nome del gruppo</label><br>
  <input type="text" name="nome_gruppo"><br>
  <label>Codice ATECO</label><br>
  <input type="text" name="codice_ateco"><br>
  <label>Descrizione del Codice ATECO</label><br>
  <input type="text" name="descrizione"><br>
  <br>
  <input type="submit" value="Crea">
  <div>*I punti contrassegnati con asterisco (*) sono obbligatori</div>
  <?php
    if (isset($_SESSION['error'])) {
      echo "<p style='color:red;'>".$_SESSION['error']."</p>";
      unset($_SESSION['error']);
    }
  ?>
</form>
  <!-- Form di Modifica cliente -->
  <form method="post" class="hidden" id="modifica_cliente" action="edit.php">
  <h2>Modifica Cliente</h2>
  <h3> Requisiti minimi di identificazione </h3>
  <label>Nome*</label><br>
  <input type="text" name="nome" required><br>
  <label>Cognome*</label><br>
  <input type="text" name="cognome" required><br>
  <label>Email*</label><br>
  <input type="email" name="email" required><br>
  <h3>Scegli i campi che vuoi modificare</h3>
  <label>Nome</label><br>
  <input type="text" name="nome"><br>
  <label>Cognome</label><br>
  <input type="text" name="cognome"><br>
  <label>Email</label><br>
  <input type="email" name="email"><br>
  <label>Telefono</label><br>
  <input type="text" name="numero_telefono"><br>
  <label>P.IVA</label><br>
  <input type="text" name="numero_piva"><br>
  <label>Data di attivazione (formato yyyy-mm-gg)</label><br>
  <input type="text" name="data_attivazione" 
  pattern="(?<=(^19\d\d|^20\d\d)-02-)(0[1-9]|2[0-9])$|^(19\d\d|20\d\d)-(0[469]|11)-(0[1-9]|1[1-9]|2[1-9]|30)$|^(19\d\d|20\d\d)-(0[13578]|12)-(0[1-9]|1[1-9]|2[1-9]|3[01])$">
  <br>
  <label>Denominazione</label><br>
  <input type="text" name="denominazione"><br>
  <label>Nome dell'azienda</label><br>
  <input type="text" name="nome_azienda"><br>
  <label>Nome del gruppo</label><br>
  <input type="text" name="nome_gruppo"><br>
  <label>Codice ATECO</label><br>
  <input type="text" name="codice_ateco"><br>
  <label>Descrizione del Codice ATECO</label><br>
  <input type="text" name="descrizione"><br>
  <br>
  <input type="submit" value="Modifica">
  <div>*I punti contrassegnati con asterisco (*) sono obbligatori</div>
  <?php
    if (isset($_SESSION['error'])) {
      echo "<p style='color:red;'>".$_SESSION['error']."</p>";
      unset($_SESSION['error']);
    }
  ?>
</form>
<!-- Form di Eliminazione cliente -->
<form method="post" class="hidden" id="elimina_cliente" action="delete.php">
  <h2>Modifica Cliente</h2>
  <h3> Requisiti minimi di identificazione </h3>
  <label>Nome*</label><br>
  <input type="text" name="nome" required><br>
  <label>Cognome*</label><br>
  <input type="text" name="cognome" required><br>
  <label>Email*</label><br>
  <input type="email" name="email" required><br>
  <input type="submit" value="Elimina">
  <div>*I punti contrassegnati con asterisco (*) sono obbligatori</div>
  <?php
    if (isset($_SESSION['error'])) {
      echo "<p style='color:red;'>".$_SESSION['error']."</p>";
      unset($_SESSION['error']);
    }
  ?>
</form>
    <p>Accesso effettuato con successo.</p>
    <a href="logout.php">Logout</a>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const forms = {
      ricerca: document.getElementById("ricerca_cliente"),
      crea: document.getElementById("crea_cliente"),
      modifica: document.getElementById("modifica_cliente"),
      elimina: document.getElementById("elimina_cliente")
    };

    const buttons = {
      ricerca: document.getElementById("btn_ricerca"),
      crea: document.getElementById("btn_crea"),
      modifica: document.getElementById("btn_modifica"),
      elimina: document.getElementById("btn_elimina")
    };

    // Function to show just one form at a time
    function showForm(formToShow){
      for (const key in forms){
        if(forms[key]){
          forms[key].classList.add("hidden");
          forms[key].classList.remove("visible");

        }
      }
      if (forms[formToShow]) {
        forms[formToShow].classList.remove("hidden");
        forms[formToShow].classList.add("visible");
      }
    }
    //Event listener dealing with buttons
    buttons.ricerca.addEventListener("click", () => showForm("ricerca"));
    buttons.crea.addEventListener("click", () => showForm("crea"));
    buttons.modifica.addEventListener("click", () => showForm("modifica"));
    buttons.elimina.addEventListener("click", () => showForm("elimina"));
  });
</script>
</body>
</html>