<?php 
// This file receives data from a form, checks whether a client is matched against the required parameters, 
// and deletes the client and most of his related data
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "db_connect.php";
// We use "?? ''" to avoid warnings
$nome=$_POST['nome'] ?? '';
$cognome=$_POST['cognome'] ?? '';
$email=$_POST['email'] ?? '';

// Minimal client identification diagnostic check
if ($nome === '' || $cognome === '' || $email === '') {
    # code...
    $_SESSION['error']="Compila tutti i campi necessari per l'eliminazione del cliente";
    header("Location: dashboard.php");
    exit;
}

// Client ID retrieval
$query="SELECT id_cliente FROM clienti c WHERE c.nome = ? AND c.cognome = ? AND c.email = ? ";
// Diagnostic print to check the prospective query with placeholders 
//(it is useful only for composed queries)
echo "<pre>$query</pre>";

$stmt = $conn->prepare($query);
// Diagnostic check to assess the preparation of the query statement
if (!$stmt) {
    # code...
    die("Errore nella preparazione della query: " . $conn->error);
}
// Diagnostic check over the prepared statement (echo does NOT work on statement objs)
error_log("DEBUG delete.php - query: $query");
error_log("DEBUG delete.php - params: nome='$nome', cognome='$cognome', email='$email'");

// Dynamic bind
$stmt->bind_param("sss", $nome, $cognome, $email);
// Diagnostic print to check the real executed query
$stmt->execute();
// Execution check
if (!$stmt->execute()) {
    $stmt->close();
    die("Errore nell'esecuzione: " . $stmt->error);
}
//Diagnostic check on results
$result=$stmt->get_result();
if ($result->num_rows === 0) {
    # code...
    $stmt->close();
    $_SESSION['error']="Nessun cliente trovato coi dati forniti.";
    header("Location: dashboard.php");
    exit;
}else {
    echo "<p>Numero risultati: " . $result->num_rows . "</p>";
}

$row= $result->fetch_assoc();
$id_cliente=(int)$row['id_cliente'];
$stmt->close();

//Records elimination
try {
    
    $conn->begin_transaction();

    //delete from telefoni_clienti
    // $stmt= $conn->prepare("DELETE FROM telefoni_clienti WHERE id_cliente= ?");
    // if (!$stmt) {
    //     throw new Exception("prepare telefoni_clienti: " . $conn->error);
    // }
    // $stmt->bind_param("i", $id_cliente);
    // $stmt->execute();

    // if (!$stmt->execute()) {
    //     throw new Exception("execute telefoni_clienti: " . $stmt->error);
    // }
    // $stmt->close();

    // // delete from partite_iva
    // $stmt= $conn->prepare("DELETE FROM partite_iva WHERE id_cliente= ?");
    // if (!$stmt) {
    //     throw new Exception("prepare partite_iva: " . $conn->error);
    // }
    // $stmt->bind_param("i", $id_cliente);
    // $stmt->execute();

    // if (!$stmt->execute()) {
    //     throw new Exception("execute partite_iva: " . $stmt->error);
    // }
    // $stmt->close();

    // delete from clienti
    $stmt= $conn->prepare("DELETE FROM clienti WHERE id_cliente= ?");
    if (!$stmt) {
        throw new Exception("prepare clienti: " . $conn->error);
    }
    $stmt->bind_param("i", $id_cliente);
    // "$stmt->execute();" seems to be a repetition if followed by "if (!$stmt->execute())"
    //$stmt->execute();

    if (!$stmt->execute()) {
        throw new Exception("execute clienti: " . $stmt->error);
    }
    $stmt->close();

    $conn->commit();
    $_SESSION['success']="Cliente eliminato con successo (id_cliente = $id_cliente).";
    header("Location: dashboard.php");
    exit;

    
} catch (Exception $e) {
    //rollback and error message
    $conn-> rollback();
    // error log for debugging
    error_log("Error delete.php - " . $e->getMessage());
    $_SESSION['error']= "Errore durante l'eliminazione: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="IT">
<head>
    <meta charset="UTF-8">
    <title>Richiesta di eliminazione - TopSoft</title>
        
  <br>
  <a href="dashboard.php">Torna alla Dashboard</a>
</body>
</html>