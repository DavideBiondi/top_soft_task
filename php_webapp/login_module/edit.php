<?php 

// This file receives data from a form, and modifies a client according to the provided parameters

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set("error_log", __DIR__ . "/php_error.log");

error_reporting(E_ALL);

//Start the MySQL session
session_start();
require_once "db_connect.php";

// We use "?? ''" to avoid warnings
// Mandatory data for client identification
$nome=$_POST['nome'] ?? '';
$cognome=$_POST['cognome'] ?? '';
$email=$_POST['email'] ?? '';

//Fields to modify
$nome_to_insert = trim($_POST['nome_to_insert'] ?? '');
$cognome_to_insert = trim($_POST['cognome_to_insert'] ?? '');
$email_to_insert = trim($_POST['email_to_insert'] ?? '');

$numero_telefono=$_POST['numero_telefono'] ?? '';
$numero_piva=$_POST['numero_piva'] ?? '';
$data_attivazione=$_POST['data_attivazione'] ?? '';
$denominazione=$_POST['denominazione'] ?? '';
$nome_azienda=$_POST['nome_azienda'] ?? '';
$nome_gruppo=$_POST['nome_gruppo'] ?? '';
// Due to N:M relationship, we were forced to change the code
// $codice_ateco=$_POST['codice_ateco'] ?? '';
// $descrizione_ateco=$_POST['descrizione'] ?? '';
$codici_selezionati = $_POST['codici_ateco'] ?? [];

// Minimal client identification diagnostic check
if ($nome === '' || $cognome === '' || $email === '') {
    # code...
    $_SESSION['error']="Compila tutti i campi necessari per l'identificazione del cliente";
    header("Location: dashboard.php");
    exit;
}

// Table specific fields
$clienti_fields = [];
$telefoni_clienti_fields = [];
$partite_iva_fields = [];
// $codici_ateco_fields = [];

// Table specific parameters
$clienti_params = [];
$telefoni_clienti_params = [];
$partite_iva_params = [];
// $codici_ateco_params = [];

// Table specific types
$clienti_types = "";
$telefoni_clienti_types = "";
$partite_iva_types = "";
// $codici_ateco_types = "";


// Validation check over the fields for clienti table
if ($nome_to_insert !== "" && strlen($nome_to_insert) >= 2) {
    $clienti_fields[] = "nome = ?";
    $clienti_params[] = $nome_to_insert;
    $clienti_types .= "s";
}

if ($cognome_to_insert !== "" && strlen($cognome_to_insert) >= 2) {
    $clienti_fields[] = "cognome = ?";
    $clienti_params[] = $cognome_to_insert;
    $clienti_types .= "s";
}

if ($email_to_insert !== "" && strlen($email_to_insert) >= 5) {
    $clienti_fields[] = "email = ?";
    $clienti_params[] = $email_to_insert;
    $clienti_types .= "s";
}

// Validation check over the fields for telefoni_clienti table
if ($numero_telefono !== "") {
    $telefoni_clienti_fields[] = "numero_telefono = ?";
    $telefoni_clienti_params[] = $numero_telefono;
    $telefoni_clienti_types .= "s";
}

// Validation check over the fields for partite_iva table
if ($numero_piva !== "") {
    $partite_iva_fields[] = "numero_piva = ?";
    $partite_iva_params[] = $numero_piva;
    $partite_iva_types .= "s";
}

if ($data_attivazione !== "") {
    $partite_iva_fields[] = "data_attivazione = ?";
    $partite_iva_params[] = $data_attivazione;
    $partite_iva_types .= "s";
}

if ($denominazione !== "") {
    $partite_iva_fields[] = "denominazione = ?";
    $partite_iva_params[] = $denominazione;
    $partite_iva_types .= "s";
}

if ($nome_azienda !== "") {
    $partite_iva_fields[] = "nome_azienda = ?";
    $partite_iva_params[] = $nome_azienda;
    $partite_iva_types .= "s";
}

if ($nome_gruppo !== "") {
    $partite_iva_fields[] = "nome_gruppo = ?";
    $partite_iva_params[] = $nome_gruppo;
    $partite_iva_types .= "s";
}

// Validation check over the fields for codici_ateco table
// if ($codice_ateco !== "") {
//     $codici_ateco_fields[] = "codice_ateco = ?";
//     $codici_ateco_params[] = $codice_ateco;
//     $codici_ateco_types .= "s";
// }
// // $descrizione_ateco=$_POST['descrizione'] ?? '';
// if ($descrizione !== "") {
//     $codici_ateco_fields[] = "descrizione = ?";
//     $codici_ateco_params[] = $descrizione;
//     $codici_ateco_types .= "s";
// }


// Fields filling diagnostic check
if (empty($clienti_fields) && empty($telefoni_clienti_fields) && empty($partite_iva_fields) && empty($codici_ateco_fields) ) {
    $_SESSION['error'] = "Nessun campo valido per la modifica del cliente.";
    header("Location: dashboard.php");
    exit;
}

// Client ID and P.IVA id retrieval
$query="SELECT c.id_cliente, p.id_piva FROM clienti c 
        INNER JOIN partite_iva p ON c.id_cliente=p.id_cliente 
        WHERE c.nome = ? AND c.cognome = ? AND c.email = ? ";

$stmt = $conn->prepare($query);
// Diagnostic check to assess the preparation of the query statement
if (!$stmt) {
    # code...
    die("Errore nella preparazione della query: " . $conn->error);
}
// Diagnostic check over the prepared statement (echo does NOT work on statement objs)
error_log("DEBUG edit.php - query: $query");
error_log("DEBUG edit.php - params: nome='$nome', cognome='$cognome', email='$email'");

// Dynamic bind
$stmt->bind_param("sss", $nome, $cognome, $email);

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
$id_piva=(int)$row['id_piva'];

// TODO: Add these couples in the related try catch 

$piva_ateco_params = [];
$piva_ateco_params[] = $id_piva;
$piva_ateco_types = "";
$piva_ateco_types .= "i";


$stmt->close();

// clienti table update
if(!empty($clienti_fields) && !empty($clienti_params) && !empty($clienti_types)) {
    try {
        // Client personal data modification query
        $update_query = "UPDATE clienti SET " . implode(", ", $clienti_fields) . " WHERE id_cliente = ?";        
        
        //Adding id_cliente as last parameter
        $clienti_params[] = $id_cliente;
        $clienti_types .= "i";
        
        $conn->begin_transaction();

        // Diagnostic check over the passed parameters
        error_log("DEBUG edit.php - UPDATE clienti: $update_query");
        error_log("DEBUG edit.php - PARAMS: " . json_encode($clienti_params));
        error_log("DEBUG edit.php - TYPES: $clienti_types");


        $stmt = $conn->prepare($update_query);
        // Diagnostic check on prepared statement
        if (!$stmt) {
            throw new Exception("prepare UPDATE clienti: " . $conn->error);
        }

        // Pre-binding diagnostic check
        $expected_params = substr_count($update_query, '?');
        $actual_params= count($clienti_params);
        error_log("DEBUG edit.php - binding check clienti: expected=$expected_params, actual=$actual_params");

        if ($expected_params !== $actual_params) {
            throw new Exception("binding UPDATE clienti: expected $expected_params params, got $actual_params");
            
        }

        // Link parameters dynamically
        $stmt->bind_param($clienti_types, ...$clienti_params);

        // DIagnostic check on query execution
        if (!$stmt->execute()) {
            throw new Exception("execute UPDATE clienti: " . $stmt->error);
        }

        $stmt->close();

        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error edit.php - " . $e->getMessage());
        $_SESSION['error'] = "Errore durante la modifica della tabella clienti: " . $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
}

// Client telephone number modification query
// $update_query_2="UPDATE telefoni_clienti SET numero_telefono= ? WHERE id_cliente=?";

// telefoni_clienti table update
if(!empty($telefoni_clienti_fields) && !empty($telefoni_clienti_params) && !empty($telefoni_clienti_types)) {
    try {
        // Client personal data modification query
        $update_query = "UPDATE telefoni_clienti SET " . implode(", ", $telefoni_clienti_fields) . " WHERE id_cliente = ?";
        
        //Adding id_cliente as last parameter
        $telefoni_clienti_params[] = $id_cliente;
        $telefoni_clienti_types .= "i";
        
        $conn->begin_transaction();

        // Diagnostic check over the passed parameters
        error_log("DEBUG edit.php - UPDATE telefoni_clienti: $update_query");
        error_log("DEBUG edit.php - PARAMS: " . json_encode($telefoni_clienti_params));
        error_log("DEBUG edit.php - TYPES: $telefoni_clienti_types");


        $stmt = $conn->prepare($update_query);
        // Diagnostic check on prepared statement
        if (!$stmt) {
            throw new Exception("prepare UPDATE telefoni_clienti: " . $conn->error);
        }

        // Pre-binding diagnostic check
        $expected_params = substr_count($update_query, '?');
        $actual_params= count($telefoni_clienti_params);
        error_log("DEBUG edit.php - binding check telefoni_clienti: expected=$expected_params, actual=$actual_params");

        if ($expected_params !== $actual_params) {
            throw new Exception("binding UPDATE telefoni_clienti: expected $expected_params params, got $actual_params");
            
        }

        // Link parameters dynamically
        $stmt->bind_param($telefoni_clienti_types, ...$telefoni_clienti_params);

        // Execute diagnostic check
        if (!$stmt->execute()) {
            throw new Exception("execute UPDATE telefoni_clienti: " . $stmt->error);
        }

        $stmt->close();

        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error edit.php - " . $e->getMessage());
        $_SESSION['error'] = "Errore durante la modifica della tabella telefoni_clienti: " . $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
}

// partite_iva table update: handles company-related data except ATECO codes
if(!empty($partite_iva_fields) && !empty($partite_iva_params) && !empty($partite_iva_types)) {
    try {
        // Company data modification query
        $update_query = "UPDATE partite_iva SET " . implode(", ", $partite_iva_fields) . " WHERE id_cliente = ?";
        
        //Adding id_cliente as last parameter
        $partite_iva_params[] = $id_cliente;
        $partite_iva_types .= "i";
        
        $conn->begin_transaction();

        // Diagnostic check over the passed parameters
        error_log("DEBUG edit.php - UPDATE partite_iva: $update_query");
        error_log("DEBUG edit.php - PARAMS: " . json_encode($partite_iva_params));
        error_log("DEBUG edit.php - TYPES: $partite_iva_types");


        $stmt = $conn->prepare($update_query);
        // Diagnostic check on prepared statement
        if (!$stmt) {
            throw new Exception("prepare UPDATE partite_iva: " . $conn->error);
        }

        // Pre-binding diagnostic check
        $expected_params = substr_count($update_query, '?');
        $actual_params= count($partite_iva_params);
        error_log("DEBUG edit.php - binding check partite_iva: expected=$expected_params, actual=$actual_params");

        if ($expected_params !== $actual_params) {
            throw new Exception("binding UPDATE partite_iva: expected $expected_params params, got $actual_params");
            
        }

        // Link parameters dynamically
        $stmt->bind_param($partite_iva_types, ...$partite_iva_params);

        // Execute diagnostic check
        if (!$stmt->execute()) {
            throw new Exception("execute UPDATE partite_iva: " . $stmt->error);
        }

        $stmt->close();

        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error edit.php - " . $e->getMessage());
        $_SESSION['error'] = "Errore durante la modifica della tabella partite_iva: " . $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
}

// codici_ateco + piva_ateco table update
// YOU DO NOT NEED A DYNAMIC QUERY HERE, because you are forced to fill all the fields of the table
if (!empty($codici_selezionati)) {
    // include/import the dictionary
    require_once "ateco_dict.php"; 
    
    try {
        $conn->begin_transaction();

        // Elimination query of ATECO ids related to a P.IVA id
        $delete_query = "DELETE FROM piva_ateco WHERE id_piva = ?";
        $stmt = $conn->prepare($delete_query);
        if (!$stmt) {
            throw new Exception("prepare DELETE piva_ateco: " . $conn->error);
        }
        $stmt->bind_param("i", $id_piva);
        if (!$stmt->execute()) {
            throw new Exception("execute DELETE piva_ateco: " . $stmt->error);
        }
        $stmt->close();

        // Insert the selected ATECO codes
        foreach ($codici_selezionati as $codice) {
            $codice = trim($codice);
            $descrizione = $ateco_dict[$codice] ?? null;

            if (!$descrizione) {
                throw new Exception("Descrizione mancante per codice ATECO: $codice");
            }

            // Insert or update the ATECO code in its main table
            $insert_ateco = "
                INSERT INTO codici_ateco (codice, descrizione)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE descrizione = VALUES(descrizione)";
            $stmt = $conn->prepare($insert_ateco);
            if (!$stmt) {
                throw new Exception("prepare INSERT codici_ateco: " . $conn->error);
            }
            $stmt->bind_param("ss", $codice, $descrizione);
            if (!$stmt->execute()) {
                throw new Exception("execute INSERT codici_ateco: " . $stmt->error);
            }

            // Retrieve the ateco id (new or already existent)
            $new_ateco_id = $conn->insert_id ?: (
                $conn->query("SELECT id_ateco FROM codici_ateco WHERE codice = '$codice'")
                      ->fetch_assoc()['id_ateco']
            );
            $stmt->close();

            // insert into the bridge table
            $insert_bridge = "INSERT IGNORE INTO piva_ateco (id_piva, id_ateco) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_bridge);
            if (!$stmt) {
                throw new Exception("prepare INSERT piva_ateco: " . $conn->error);
            }
            $stmt->bind_param("ii", $id_piva, $new_ateco_id);
            if (!$stmt->execute()) {
                throw new Exception("execute INSERT piva_ateco: " . $stmt->error);
            }
            $stmt->close();
        }


    }catch (Exception $e) {
        $conn->rollback();
        error_log("Error edit.php - " . $e->getMessage());
        $_SESSION['error'] = "Errore durante la modifica dei codici ATECO: " . $e->getMessage();
        header("Location: dashboard.php");
        exit;
    }
}


$conn->commit();
if (!isset($_SESSION['error'])) {
    $_SESSION['success'] = "Dati del cliente aggiornati con successo.";
    header("Location: dashboard.php");
    exit;
}


// try {
//     // Enforce the modification as soon as possible
//     $conn->begin_transaction();

//     // UPDATE clienti
//     $stmt=$conn->prepare($update_query);

//     // Conversion of non-truthy and whitespaces into NULLs
//     $nome_to_insert = trim($nome_to_insert) ?: NULL;
//     $cognome_to_insert = trim($cognome_to_insert) ?: NULL;
//     $email_to_insert = trim($nome_to_insert) ?: NULL;


//     // Diagnostic check to assess the preparation of the query statement
//     if (!$stmt) {
//         #It is better not to use a "die" operation inside a transaction block
//         #so that you can rollback in the catch block of your code
//         throw new Exception("prepare UPDATE clienti: " . $conn->error);
//     }
//     // Diagnostic check over the prepared statement (echo does NOT work on statement objs)
//     error_log("DEBUG edit.php - query: $update_query");
//     error_log("DEBUG edit.php - params: 
//     nome='$nome_to_insert', cognome='$cognome_to_insert', email='$email_to_insert', id_cliente='$id_cliente'");

//     // Dynamic bind
//     $stmt->bind_param("sssi", $nome_to_insert, $cognome_to_insert, $email_to_insert, $id_cliente);

//     // Execution check (make sure you do NOT execute twice in the nearby lines)
//     if (!$stmt->execute()) {
//         throw new Exception("execute UPDATE ON clienti: " . $stmt->error);
//     }
//     $stmt->close();

//     // UPDATE telefoni_clienti
//     $stmt=$conn->prepare($update_query_2);

//     // Conversion of non-truthy and whitespaces into NULLs
//     $numero_telefono = trim($numero_telefono) ?: NULL;

//     // Diagnostic check to assess the preparation of the query statement
//     if (!$stmt) {
//         #It is better not to use a "die" operation inside a transaction block
//         #so that you can rollback in the catch block of your code
//         throw new Exception("prepare UPDATE telefoni_clienti: " . $conn->error);
//     }
//     // Diagnostic check over the prepared statement (echo does NOT work on statement objs)
//     error_log("DEBUG edit.php - query: $update_query_2");
//     error_log("DEBUG edit.php - params: 
//     numero_telefono= '$numero_telefono', id_cliente='$id_cliente'");

//     // Dynamic bind
//     $stmt->bind_param("si", $numero_telefono, $id_cliente);

//     // Execution check (make sure you do NOT execute twice in the nearby lines)
//     if (!$stmt->execute()) {
//         throw new Exception("execute UPDATE ON telefoni_clienti: " . $stmt->error);
//     }
//     $stmt->close();

//     // Successful queries
//     $conn->commit();
//     $_SESSION['success']="Cliente ed eventuali dati aziendali modificati con successo";
//     header("Location: dashboard.php");
//     exit;

// }catch (Exception $e) {
//     //rollback and error message
//     $conn-> rollback();
//     // error log for debugging
//     error_log("Error edit.php - " . $e->getMessage());
//     $_SESSION['error']= "Errore durante la modifica: " . $e->getMessage();
//     header("Location: dashboard.php");
//     exit;
// }





?>