<?php
// filepath: c:\xampp\htdocs\bigmatch\bigmatch\deleteRec.php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerId = $_POST['playerId'];

    // Validate playerId
    if (empty($playerId)) {
        echo json_encode(['success' => false, 'message' => 'Player ID is required']);
        exit;
    }

    // Delete the record
    $query = "DELETE FROM bowling_figures WHERE player_id = '$playerId'";
    if (Database::q($query)) {
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete record']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>