<?php 
// This file receives data from a form, and create a client with the required parameters, 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "db_connect.php";
require_once "sanitize_csv.php";
// We use "?? ''" to avoid warnings
// Mandatory data
$nome=sanitize_csv_input($_POST['nome'] ?? '');
$cognome=sanitize_csv_input($_POST['cognome'] ?? '');
$email=$_POST['email'] ?? '';
$numero_piva=sanitize_csv_input($_POST['numero_piva'] ?? '');
$nome_azienda=sanitize_csv_input($_POST['nome_azienda'] ?? '');

//Non mandatory
$numero_telefono=sanitize_csv_input($_POST['numero_telefono'] ?? '');
$nome_gruppo=sanitize_csv_input($_POST['nome_gruppo'] ?? '');
$data_attivazione=$_POST['data_attivazione'] ?? '';
$denominazione=sanitize_csv_input($_POST['denominazione'] ?? '');
$descrizione_ateco=sanitize_csv_input($_POST['descrizione'] ?? '');
$codice_ateco=$_POST['codice_ateco'] ?? '';

// Minimal client creation diagnostic check
if ($nome === '' || $cognome === '' || $email === '' || $numero_piva === '' || $nome_azienda === '') {
    # code...
    $_SESSION['error']="Compila tutti i campi necessari per la creazione del cliente";
    header("Location: dashboard.php");
    exit;
}

// Static query for clienti table
$query = "INSERT INTO clienti (nome, cognome, email) VALUES (?, ?, ?)";

// Make the addition of the client instant, otherwise partite_iva and telefoni_clienti will not be
// populated. So we start the transaction before the prepared statement, and the commit after execution 
$conn->begin_transaction();

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

// Execution check
// Resolving the id_cliente
if ($stmt->execute()) {
    echo "<p>Cliente creato con successo (ID = $new_id).</p>";
}else {
    $stmt->close();
    die("Errore nella creazione del cliente: " . $stmt->error);
}
// insert_id MUST BE OBTAINED AFTER execute()!!! Otherwise its value will be 0!
$new_id = $conn->insert_id;

$stmt->close();
// Make the creation of the record instantly, so that you do not have problems with the tables
// depending on clienti table
$conn->commit();

// Resolving the id_cliente
//$id_client_query="SELECT id_cliente FROM clienti c WHERE c.email = ? ";

try {
    $conn->begin_transaction();
    // Creation of telephone number in telefoni_clienti
    $stmt= $conn->prepare("INSERT INTO telefoni_clienti (id_cliente, numero_telefono) VALUES
    (?, ?);");

    // Conversion of non-truthy and whitespaces into NULLs
    $numero_telefono = trim($numero_telefono) ?: NULL;

    if (!$stmt) {
        throw new Exception("prepare the creation of the telephone number: " . $conn->error);
    }
    $stmt->bind_param("is", $new_id, $numero_telefono);

    if (!$stmt->execute()) {
        throw new Exception("execute creation of telephone number: " . $stmt->error);
    }
    $stmt->close();

    // Creation of the client data in partite_iva
    $stmt = $conn->prepare("INSERT INTO 
    partite_iva (id_cliente, numero_piva, nome_azienda, nome_gruppo, denominazione, data_attivazione) 
    VALUES (?, ?, ?, ?, ?, ?)");

    // Conversion of non-truthy and whitespaces into NULLs
    $nome_gruppo = trim($nome_gruppo) ?: NULL;
    $denominazione = trim($denominazione) ?: NULL;
    $data_attivazione = trim($data_attivazione) ?: NULL;


    if (!$stmt) {
        throw new Exception("prepare the creation of company data: " . $conn->error);
    }
    $stmt->bind_param("isssss", 
                     $new_id, $numero_piva, $nome_azienda, 
                     $nome_gruppo, $denominazione, $data_attivazione);

    if (!$stmt->execute()) {
        throw new Exception("execute creation of company data: " . $stmt->error);
    }
    // Resolving the id_piva
    $new_piva_id = $conn->insert_id;
    echo "<p>P.IVA creata con successo (ID = $new_piva_id).</p>";

    
    
    $stmt->close();

    // Creation of the client data in codici_ateco
    // Avoiding duplication of ATECO codes
    $stmt = $conn->prepare("INSERT INTO codici_ateco(codice, descrizione) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE id_ateco = LAST_INSERT_ID(id_ateco)");
    
    // Conversion of non-truthy and whitespaces into NULLs
    $codice_ateco = trim($codice_ateco) ?: NULL;
    $descrizione_ateco = trim($descrizione_ateco) ?: NULL;

    if (!$stmt) {
        throw new Exception("prepare the creation of ATECO data: " . $conn->error);
    }
    $stmt->bind_param("ss", $codice_ateco, $descrizione_ateco);

    if (!$stmt->execute()) {
        throw new Exception("execute creation of ATECO data: " . $stmt->error);
    }
    // Resolving the id_ateco
    $new_ateco_id = $conn->insert_id;
    echo "<p>Codice ATECO creato con successo (ID = $new_ateco_id).</p>";
    

    $stmt->close();

    // Creation of the company data in piva_ateco
    $stmt = $conn->prepare("INSERT INTO piva_ateco (id_piva, id_ateco) VALUES (?, ?)");
    
    if (!$stmt) {
        throw new Exception("prepare the creation of IVA-ATECO data: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $new_piva_id, $new_ateco_id);

    if (!$stmt->execute()) {
        throw new Exception("execute creation of IVA-ATECO data: " . $stmt->error);
    }
        echo "<p>IDs IVA-ATECO inseriti con successo (IDs = $new_piva_id, $new_ateco_id).</p>";
    

    $stmt->close();
    
    // Successful queries
    $conn->commit();
    $_SESSION['success']="Cliente e dati aziendali creati con successo 
    (id_cliente, id_piva = $new_id, $new_piva_id).";
    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {
    //rollback and error message
    $conn-> rollback();
    // error log for debugging
    error_log("Error create.php - " . $e->getMessage());
    $_SESSION['error']= "Errore durante la creazione: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}


?>