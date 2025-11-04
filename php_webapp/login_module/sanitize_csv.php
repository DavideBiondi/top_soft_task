<?php 
// Function to remove problematic chars from data in case of csv export
function sanitize_csv_input($str){
    return preg_replace('/[;,:\t\"\n\r><|]/', '', $str);
}
?>