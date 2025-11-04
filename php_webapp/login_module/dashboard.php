<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    # code...
    header("Location: login.php");
    exit;
}
// Import the dictionary with ATECO codes
require_once "ateco_dict.php";
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
  pattern="(?<=(^19\d\d|^20\d\d)-02-)(0[1-9]|2[0-9])$|^(19\d\d|20\d\d)-(0[469]|11)-(0[1-9]|1[0-9]|2[0-9]|30)$|^(19\d\d|20\d\d)-(0[13578]|12)-(0[1-9]|1[0-9]|2[0-9]|3[01])$">
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
  <input type="text" name="nome" required pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Cognome*</label><br>
  <input type="text" name="cognome" required pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Telefono</label><br>
  <input type="text" name="numero_telefono" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Email*</label><br>
  <input type="email" name="email" required><br>
  <label>P.IVA*</label><br>
  <input type="text" name="numero_piva" required pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Nome dell'azienda*</label><br>
  <input type="text" name="nome_azienda" required pattern='^[^;,:\t\"\n\r><|]+'><br>
  
  <label>Data di attivazione (formato yyyy-mm-gg)</label><br>
  <input type="text" name="data_attivazione" 
  pattern="(?<=(^19\d\d|^20\d\d)-02-)(0[1-9]|2[0-9])$|^(19\d\d|20\d\d)-(0[469]|11)-(0[1-9]|1[1-9]|2[1-9]|30)$|^(19\d\d|20\d\d)-(0[13578]|12)-(0[1-9]|1[1-9]|2[1-9]|3[01])$">
  <br>
  <label>Denominazione</label><br>
  <input type="text" name="denominazione" maxlength="4" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Nome del gruppo</label><br>
  <input type="text" name="nome_gruppo" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Codice ATECO</label><br>
  <input type="text" name="codice_ateco" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Descrizione del Codice ATECO</label><br>
  <input type="text" name="descrizione" pattern='^[^;,:\t\"\n\r><|]+'><br>
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
  <input type="text" name="nome" required pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Cognome*</label><br>
  <input type="text" name="cognome" required pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Email*</label><br>
  <input type="email" name="email" required><br>
  <h3>Scegli i campi che vuoi modificare</h3>
  <label>Nome</label><br>
  <input type="text" name="nome_to_insert" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Cognome</label><br>
  <input type="text" name="cognome_to_insert" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Email</label><br>
  <input type="email" name="email_to_insert"><br>
  <label>Telefono</label><br>
  <input type="text" name="numero_telefono" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>P.IVA</label><br>
  <input type="text" name="numero_piva" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Data di attivazione (formato yyyy-mm-gg)</label><br>
  <input type="text" name="data_attivazione" 
  pattern="(?<=(^19\d\d|^20\d\d)-02-)(0[1-9]|2[0-9])$|^(19\d\d|20\d\d)-(0[469]|11)-(0[1-9]|1[1-9]|2[1-9]|30)$|^(19\d\d|20\d\d)-(0[13578]|12)-(0[1-9]|1[1-9]|2[1-9]|3[01])$">
  <br>
  <label>Denominazione</label><br>
  <input type="text" name="denominazione" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Nome dell'azienda</label><br>
  <input type="text" name="nome_azienda" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <label>Nome del gruppo</label><br>
  <input type="text" name="nome_gruppo" pattern='^[^;,:\t\"\n\r><|]+'><br>
  <!--<label>Codice ATECO</label><br>
  <input type="text" name="codice_ateco"><br>
  <label>Descrizione del Codice ATECO</label><br>
  <input type="text" name="descrizione"><br>
  <br> -->
  <!-- <label>Codici ATECO associati</label><br>
<select name="id_ateco[]" multiple size="5">
  <option value="1">62.01 - Produzione di software non connesso all’edizione</option>
  <option value="2">63.11 - Elaborazione dei dati, hosting e attività connesse</option>
  <option value="3">70.22 - Consulenza imprenditoriale e amministrativo-gestionale</option>
  <option value="4">62.09 - Altre attività dei servizi connessi alle tecnologie informatiche</option>
  <option value="5">57.21 - Produzione di software di tipo embedded</option>
</select> -->
<label>Codici ATECO disponibili</label><br>
<select name="codici_ateco[]" multiple size="7">
<?php
foreach ($ateco_dict as $codice => $descrizione) {
    echo "<option value='$codice'>$codice - $descrizione</option>";
}
?>
</select>  
  <small>
    Tieni premuto <b>Ctrl</b> (Windows/Linux) o <b>Cmd</b> (Mac) per selezionare più codici ATECO.
  </small>
  <br><br>

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
  <h2>Elimina Cliente</h2>
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