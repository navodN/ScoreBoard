<?php
// filepath: c:\xampp\htdocs\bigmatch\bigmatch\ssckVCKBallProcess.php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bowlerId = $_POST['bowlerId'];
    $bowlingOrder = $_POST['bowlingOrder'];
    $overs = $_POST['overs'];
    $maidens = $_POST['maidens'];
    $runsConceded = $_POST['runsConceded'];
    $wickets = $_POST['wickets'];
    $wides = $_POST['wides'];
    $noBalls = $_POST['noBalls'];
    $dots = $_POST['dots'];

    // Validate input
    if (empty($bowlerId) || empty($bowlingOrder)) {
        echo json_encode(['success' => false, 'message' => 'Bowler and Bowling Order are required']);
        exit;
    }

    //Setting the player bowled
    $bowled = 1;

    // Check if record exists
    $existingRecord = Database::getValue("SELECT player_id FROM bowling_figures WHERE player_id = '$bowlerId'");

    if ($existingRecord) {
        // Update existing record
        $query = "UPDATE bowling_figures 
                  SET bowling_ord = '$bowlingOrder', is_bowled = '$bowled', overs = '$overs', maidens = '$maidens', runs_conceded = '$runsConceded', 
                      wickets = '$wickets', wides = '$wides', no_balls = '$noBalls', dots = '$dots' 
                  WHERE player_id = '$bowlerId'";
    } else {
        // Insert new record
        $query = "INSERT INTO bowling_figures 
                  (player_id, bowling_ord, is_bowled, overs, maidens, runs_conceded, wickets, wides, no_balls, dots) 
                  VALUES 
                  ('$bowlerId', '$bowlingOrder', '$bowled', '$overs', '$maidens', '$runsConceded', '$wickets', '$wides', '$noBalls', '$dots')";
    }

    // Execute query and handle errors
    if (Database::q($query)) {
        echo ("success");
    } else {
        echo "Error: " . Database::$connection->error;
    }
} else {
    echo 'Invalid request method';
}
