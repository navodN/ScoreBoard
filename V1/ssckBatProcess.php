<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerId = $_POST['playerId'];
    $position = $_POST['position'];
    $runs = $_POST['runs'];
    $balls = $_POST['balls'];
    $fours = $_POST['fours'];
    $sixes = $_POST['sixes'];
    $isOut = $_POST['isOut'];
    $dismissalType = $_POST['dismissalType'];
    $bowledBy = !empty($_POST['bowledBy']) ? $_POST['bowledBy'] : null; // Set to null if empty
    $caughtBy = !empty($_POST['caughtBy']) ? $_POST['caughtBy'] : null; // Set to null if empty

    $isBatted = 1;
    $dismissalDetails = null;

    // Prepare dismissal details
    if ($isOut === 'true') {
        if (in_array($dismissalType, ['bowled', 'lbw', 'run_out', 'stumped', 'hit_wicket', 'retired_hurt'])) {
            $bowledByName = Database::getValue("SELECT first_name FROM players WHERE player_id = '$bowledBy'");
            $dismissalDetails = "b $bowledByName";
            $caughtBy = null; // Reset caughtBy if bowled
        } elseif ($dismissalType === 'caught') {
            $bowledByName = Database::getValue("SELECT first_name FROM players WHERE player_id = '$bowledBy'");
            $caughtByName = Database::getValue("SELECT first_name FROM players WHERE player_id = '$caughtBy'");
            $dismissalDetails = "c $caughtByName b $bowledByName";
        } else {
            $dismissalDetails = $dismissalType; // Handle other dismissal types
        }
        $out = 1; // Player is out
    } else {
        $dismissalDetails = 'not out'; // Player is not out
        $out = 0; // Player is not out
    }

    // Check if record exists
    $existingRecord = Database::getValue("SELECT player_id FROM batting_scores WHERE player_id = '$playerId'");

    if ($existingRecord) {
        // Update existing record
        $query = "UPDATE batting_scores 
                  SET position = '$position', is_batted = '$isBatted', runs = '$runs', balls_faced = '$balls', 
                      fours = '$fours', sixes = '$sixes', is_out = '$out', dismissal_type = '$dismissalType', 
                      dismissal_status = '$dismissalDetails', bowled_by = " . ($bowledBy ? "'$bowledBy'" : "NULL") . ", 
                      caught_by = " . ($caughtBy ? "'$caughtBy'" : "NULL") . " 
                  WHERE player_id = '$playerId'";
    } else {
        // Insert new record
        $query = "INSERT INTO batting_scores 
                  (player_id, position, is_batted, runs, balls_faced, fours, sixes, is_out, dismissal_type, 
                   dismissal_status, bowled_by, caught_by) 
                  VALUES 
                  ('$playerId', '$position', '$isBatted', '$runs', '$balls', '$fours', '$sixes', '$out', 
                   '$dismissalType', '$dismissalDetails', " . ($bowledBy ? "'$bowledBy'" : "NULL") . ", 
                   " . ($caughtBy ? "'$caughtBy'" : "NULL") . ")";
    }

    // Execute query and handle errors
    if (Database::q($query)) {
        echo "success";
    } else {
        echo "Error: " . Database::$connection->error;
    }
} else {
    echo "Invalid request method";
}
?>