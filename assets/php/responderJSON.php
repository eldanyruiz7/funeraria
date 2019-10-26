<?php
function responder($response, $mysqli)
{
    $response['error'] = $mysqli->error;
    header('Content-Type: application/json');
    echo json_encode($response, JSON_FORCE_OBJECT);
    $mysqli->close();
    exit;
}
?>
