<?php 
// Output buffering activation
ob_start();

// This file receives data from a form, checks whether a client is matched against the required parameters, 
// and returns a table
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set("error_log", __DIR__ . "/php_error.log");

// Number of buffers opened diagnostic check
error_log("DEBUG search.php - number of buffers opened after ob_start(): " . ob_get_level());


session_start();
require_once "db_connect.php";

//CSV export option
$export_csv = isset($_POST['export_csv']);

// We use "?? ''" to avoid warnings
$nome=$_POST['nome'] ?? '';
$cognome=$_POST['cognome'] ?? '';
$email=$_POST['email'] ?? '';
$data_inserimento=$_POST['data_inserimento'] ?? '';
$numero_telefono=$_POST['numero_telefono'] ?? '';
$numero_piva=$_POST['numero_piva'] ?? '';
$nome_azienda=$_POST['nome_azienda'] ?? '';
$nome_gruppo=$_POST['nome_gruppo'] ?? '';
$codice_ateco=$_POST['codice_ateco'] ?? '';

$conditions =[];
$params=[];
$types="";

// Condizioni dinamiche
if ($nome !== "") {
    # code...
    $conditions[] = "c.nome LIKE ?";
    $params[]= "%$nome%";
    $types .= "s";
}

if ($cognome !== "") {
    # code...
    $conditions[] = "c.cognome LIKE ?";
    $params[]= "%$cognome%";
    $types .= "s";
}

if ($email !== "") {
    # code...
    $conditions[] = "c.email LIKE ?";
    $params[]= "%$email%";
    $types .= "s";
}

if ($data_inserimento !== "") {
    # code...
    $conditions[] = "c.data_inserimento LIKE ?";
    $params[]= "%$data_inserimento%";
    $types .= "s";
}

if ($numero_telefono !== "") {
    # code...
    $conditions[] = "t.numero_telefono LIKE ?";
    $params[]= "%$numero_telefono%";
    $types .= "s";
}

if ($numero_piva !== "") {
    # code...
    $conditions[] = "p.numero_piva LIKE ?";
    $params[]= "%$numero_piva%";
    $types .= "s";
}

if ($nome_azienda !== "") {
    # code...
    $conditions[] = "p.nome_azienda LIKE ?";
    $params[]= "%$nome_azienda%";
    $types .= "s";
}

if ($nome_gruppo !== "") {
    # code...
    $conditions[] = "p.nome_gruppo LIKE ?";
    $params[]= "%$nome_gruppo%";
    $types .= "s";
}

if ($codice_ateco !== "") {
    # code...
    $conditions[] = "ca.codice LIKE ?";
    $params[]= "%$codice_ateco%";
    $types .= "s";
}

//If none of the fields is compiled, go back

if (empty($conditions)) {
    # code...
    $_SESSION['error']="Compila almeno un campo per la ricerca.";
    header("Location: dashboard.php");
    exit;
}

// Dynamic query
$query = "SELECT c.nome, c.cognome, c.email, p.numero_piva, t.numero_telefono, ca.codice AS codice_ateco, ca.descrizione FROM clienti c 
          LEFT JOIN telefoni_clienti t ON c.id_cliente = t.id_cliente
          LEFT JOIN partite_iva p ON c.id_cliente=p.id_cliente 
          LEFT JOIN piva_ateco pa ON p.id_piva=pa.id_piva 
          LEFT JOIN codici_ateco ca ON pa.id_ateco=ca.id_ateco 
          ";

// Dianostic check on the filling of conditions
if (!empty($conditions)) {
    # code...
    $query .= " WHERE " . implode(" AND ", $conditions);
}else{
    $_SESSION['error'] = "Errore: l'array conditions è evidentemente vuoto!";
}
$query .= " ORDER BY c.nome;";

// Diagnostic print to check the real executed query
echo "<pre>$query</pre>";

// Diagnostic check over the passed parameters
error_log("DEBUG search.php - SEARCH/READ clienti(joined): $query");
error_log("DEBUG search.php - PARAMS: " . json_encode($params));
error_log("DEBUG search.php - TYPES: $types");

$stmt = $conn->prepare($query);
// Diagnostic check to assess the preparation of the query statement
if (!$stmt) {
    # code...
    die("Errore nella preparazione della query: " . $conn->error);
}

// Pre-binding diagnostic check
$expected_params = substr_count($query, '?');
$actual_params= count($params);
error_log("DEBUG search.php - binding check clienti: expected=$expected_params, actual=$actual_params");

// Dynamic bind
$stmt->bind_param($types, ...$params);
$stmt->execute();
// Execution check
if (!$stmt->execute()) {
    die("Errore nell'esecuzione: " . $stmt->error);
}
$result=$stmt->get_result();

if ($result->num_rows >= 1) {
    # code...
    //$_SESSION['loggedin'] = true;
    //$_SESSION['username'] = $username;
    //header("Location: dashboard.php");
    // row number check
    echo "<p>Numero risultati: " . $result->num_rows . "</p>"; 
    // "exit;" here will make the html code at the end of the page unreacheable
}else{
    $_SESSION['error'] = "La tua ricerca ha prodotto 0 risultati!";
    header("Location: dashboard.php");
    exit;
}

// === EXPORT CSV ===
if ($export_csv) {
  if ($result->num_rows > 0) {

      // Buffer cleanse diagnostic check
      if (ob_get_level() > 0) {
        ob_end_clean();
      }
      // Set HTTP headers for direct download
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename="clienti_export.csv"');

      $output = fopen('php://output', 'w');

      // Columns headers (avoid whitespaces otherwise they will be enclosed in double quotes)
      fputcsv($output, ['Nome', 'Cognome', 'Email', 'Telefono', 'P._IVA', 'Codice_ATECO', 'Descrizione']);

      // Row per row
      while ($row = $result->fetch_assoc()) {
          fputcsv($output, [
              $row['nome'] ?? '',
              $row['cognome'] ?? '',
              $row['email'] ?? '',
              $row['numero_telefono'] ?? '',
              $row['numero_piva'] ?? '',
              $row['codice_ateco'] ?? '',
              $row['descrizione'] ?? ''
          ]);
      }

      fclose($output);
      exit; // Stop HTML rendering
  } else {
      $_SESSION['error'] = "Nessun risultato da esportare.";
      header("Location: dashboard.php");
      exit;
  }
}

?>
<!DOCTYPE html>
<html lang="IT">
<head>
    <meta charset="UTF-8">
    <title>Risultati ricerca - TopSoft</title>
    <style>
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #0078d7;
      color: white;
    }
  </style>
</head>
<body>
  <h1>Risultati ricerca</h1>
  <?php if ($result->num_rows > 0): ?>

    <form method="post" action="search.php">
      <!-- Manteniamo i parametri della ricerca -->
      <input type="hidden" name="nome" value="<?= htmlspecialchars($nome) ?>">
      <input type="hidden" name="cognome" value="<?= htmlspecialchars($cognome) ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <input type="hidden" name="numero_piva" value="<?= htmlspecialchars($numero_piva) ?>">
      <input type="hidden" name="data_inserimento" value="<?= htmlspecialchars($data_inserimento) ?>">
      <input type="hidden" name="numero_telefono" value="<?= htmlspecialchars($numero_telefono) ?>">
      <input type="hidden" name="nome_azienda" value="<?= htmlspecialchars($nome_azienda) ?>">
      <input type="hidden" name="nome_gruppo" value="<?= htmlspecialchars($nome_gruppo) ?>">
      <input type="hidden" name="codice_ateco" value="<?= htmlspecialchars($codice_ateco) ?>">


      <button type="submit" name="export_csv" value="1"> Esporta CSV</button>
    </form>

    <table>
      <tr>
        <th>Nome</th>
        <th>Cognome</th>
        <th>Email</th>
        <th>Telefono</th>
        <th>P. IVA</th>
        <th>Codice ATECO</th>
        <th>Descrizione</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><?= htmlspecialchars($row['cognome']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['numero_telefono']) ?></td>
          <td><?= htmlspecialchars($row['numero_piva']) ?></td>
          <td><?= htmlspecialchars($row['codice_ateco']) ?></td>
          <td><?= htmlspecialchars($row['descrizione']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>Nessun risultato trovato.</p>
  <?php endif; ?>
  <br>
  
  <a href="dashboard.php">Torna alla Dashboard</a>
</body>
</html>