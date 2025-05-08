<?php

include "connection.php";

// Get match details
$match = Database::getAssoc("SELECT * FROM `matches` ORDER BY `match_id` DESC LIMIT 1")[0];
$toss_winner_id = $match['toss_winner_id'];
$toss_decision = $match['toss_decision'];
$batting_first_id = ($toss_decision == 'bat') ? $toss_winner_id : ($match['home_team_id'] == $toss_winner_id ? $match['away_team_id'] : $match['home_team_id']);
$bowling_first_id = ($batting_first_id == $match['home_team_id']) ? $match['away_team_id'] : $match['home_team_id'];

// Get team names
$team1 = Database::getAssoc("SELECT * FROM `teams` WHERE `team_id` = $batting_first_id")[0];
$team2 = Database::getAssoc("SELECT * FROM `teams` WHERE `team_id` = $bowling_first_id")[0];

// Function to calculate batting totals
function calculateBattingTotals($team_id) {
    $result = Database::q("SELECT p.*, b.* 
        FROM players p 
        LEFT JOIN batting_scores b ON p.player_id = b.player_id 
        WHERE p.team_id = $team_id AND b.is_batted = 1 
        ORDER BY b.position ASC");
    
    $totals = [
        'runs' => 0,
        'balls' => 0,
        'fours' => 0,
        'sixes' => 0,
        'wickets' => 0,
        'extras' => 0,
        'players' => []
    ];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $totals['runs'] += (int)$row['runs'];
        $totals['balls'] += (int)$row['balls_faced'];
        $totals['fours'] += (int)$row['fours'];
        $totals['sixes'] += (int)$row['sixes'];
        if ($row['is_out']) {
            $totals['wickets']++;
        }
        $totals['players'][] = $row;
    }
    
    $totals['strike_rate'] = ($totals['balls'] > 0) ? round(($totals['runs'] / $totals['balls']) * 100, 2) : 0;
    $totals['run_rate'] = ($totals['balls'] > 0) ? round($totals['runs'] / ($totals['balls'] / 6), 2) : 0;
    $totals['overs'] = floor($totals['balls'] / 6) . '.' . ($totals['balls'] % 6);
    
    return $totals;
}

// Function to get yet to bat players
function getYetToBat($team_id) {
    $result = Database::q("SELECT first_name, last_name FROM players p 
        LEFT JOIN batting_scores b ON p.player_id = b.player_id 
        WHERE p.team_id = $team_id AND (b.is_batted = 0 OR b.is_batted IS NULL)");
    
    $names = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $names[] = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
    }
    return !empty($names) ? implode(', ', $names) : 'None';
}

// Function to get bowling figures
function getBowlingFigures($bowling_team_id) {
    $result = Database::q("SELECT p.*, b.* 
        FROM players p 
        LEFT JOIN bowling_figures b ON p.player_id = b.player_id 
        WHERE p.team_id = $bowling_team_id AND b.is_bowled = 1 
        ORDER BY b.overs ASC");
    
    $figures = [
        'rows' => [],
        'total_balls' => 0,
        'extras' => 0
    ];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $figures['rows'][] = $row;
        
        // Calculate total balls bowled
        $overs_parts = explode('.', $row['overs']);
        $whole_overs = isset($overs_parts[0]) ? (int)$overs_parts[0] : 0;
        $balls = isset($overs_parts[1]) ? (int)$overs_parts[1] : 0;
        $figures['total_balls'] += ($whole_overs * 6) + $balls;
        
        $figures['extras'] += (isset($row['wides']) ? (int)$row['wides'] : 0) + (isset($row['no_balls']) ? (int)$row['no_balls'] : 0);
    }
    
    $figures['total_overs'] = floor($figures['total_balls'] / 6) . '.' . ($figures['total_balls'] % 6);
    
    return $figures;
}

// Get data for both innings
$first_innings = calculateBattingTotals($batting_first_id);
$first_innings['yet_to_bat'] = getYetToBat($batting_first_id);
$first_innings_bowling = getBowlingFigures($bowling_first_id);

// Check if second innings has started (opponent has batted)
$second_innings_started = Database::getValue("SELECT COUNT(*) FROM players p 
    JOIN batting_scores b ON p.player_id = b.player_id 
    WHERE p.team_id = $bowling_first_id AND b.is_batted = 1");

if ($second_innings_started > 0) {
    $second_innings = calculateBattingTotals($bowling_first_id);
    $second_innings['yet_to_bat'] = getYetToBat($bowling_first_id);
    $second_innings_bowling = getBowlingFigures($batting_first_id);
}

// Determine current match status
$match_status = "1st Innings in progress";
if ($second_innings_started > 0) {
    $match_status = "2nd Innings in progress";
    if ($second_innings['wickets'] == 10 || $second_innings['balls'] >= 120) { // Assuming 20 overs
        $match_status = "Match Completed";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket Live Score - Match Center</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a237e;
            --secondary: #2962ff;
            --accent: #ff6d00;
            --light: #f5f5f5;
            --dark: #263238;
            --success: #00c853;
            --warning: #ffd600;
            --danger: #d50000;
            --india: #0052cc;
            --australia: #ffca28;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: rgb(8, 27, 54);
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .site-header {
            background-color: var(--primary);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Match Banner */
        .match-banner {
            background: linear-gradient(135deg, var(--india) 0%, var(--primary) 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .match-title {
            font-size: 1.8rem;
            margin-bottom: 15px;
            text-align: center;
        }

        .match-teams {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-bottom: 20px;
        }

        .team {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .team-flag {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .team-name {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .team-score {
            font-size: 1.5rem;
            margin-top: 5px;
        }

        .versus {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 0 20px;
        }

        .match-status {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin: 0 auto;
        }

        .match-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .match-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .live-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            background-color: var(--danger);
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .live-indicator .pulse {
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        /* Scorecard */
        .scorecard {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .scorecard-header {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .scorecard-title {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .scorecard-body {
            padding: 0;
        }

        .scorecard-summary {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .scorecard-table {
            width: 100%;
            border-collapse: collapse;
        }

        .scorecard-table th,
        .scorecard-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .scorecard-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }

        .scorecard-table tr:hover {
            background-color: #f8f9fa;
        }

        .scorecard-table tr.striker {
            background-color: rgba(41, 98, 255, 0.05);
        }

        .player-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .player-img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dismissal {
            font-size: 0.85rem;
            color: #666;
        }

        .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-out {
            background-color: #ffecec;
            color: var(--danger);
        }

        .status-notout {
            background-color: #e6f7ee;
            color: var(--success);
        }

        .scorecard-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
        }

        .extras,
        .fall-of-wickets {
            margin-bottom: 10px;
        }

        /* Current Over */
        .current-over {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 15px 20px;
        }

        .current-over-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .over-balls {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ball {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .ball-dot {
            background-color: #e0e0e0;
            color: #333;
        }

        .ball-run {
            background-color: #bbdefb;
            color: #1565c0;
        }

        .ball-four {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .ball-six {
            background-color: #ffcc80;
            color: #ef6c00;
        }

        .ball-wicket {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .ball-extra {
            background-color: #d1c4e9;
            color: #4527a0;
        }

        /* Commentary */
        .commentary {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .commentary-header {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
        }

        .commentary-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .commentary-body {
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .commentary-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .commentary-item:last-child {
            border-bottom: none;
        }

        .commentary-over {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .commentary-text {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Two Column Layout */
        .two-column {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .main-column {
            flex: 2;
        }

        .side-column {
            flex: 1;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .two-column {
                flex-direction: column;
            }

            .match-teams {
                flex-direction: column;
                gap: 20px;
            }

            .versus {
                margin: 10px 0;
            }

            .match-meta {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Match Banner -->
        <div class="match-banner">
            <h1 class="match-title"><?php echo $team1['team_name'] ?> Vs. <?php echo $team2['team_name'] ?> - Battle of the Babes</h1>
            <div class="match-teams">
                <div class="team">
                    <div class="team-flag">
                    <img src="st_sylvester's.png" alt="" width="30" height="50">

                    </div>
                    <div class="team-name"><?php echo $team1['team_name']; ?></div>
                    <div class="team-score">
                        <?php if ($batting_first_id == $team1['team_id']): ?>
                            <?php echo $first_innings['runs'] . '/' . $first_innings['wickets'] . ' (' . $first_innings['overs'] . ' ov)' ?>
                        <?php elseif (isset($second_innings) && $bowling_first_id == $team1['team_id']): ?>
                            <?php echo $second_innings['runs'] . '/' . $second_innings['wickets'] . ' (' . $second_innings['overs'] . ' ov)' ?>
                        <?php else: ?>
                            Yet to bat
                        <?php endif; ?>
                    </div>
                </div>
                <div class="versus">VS</div>
                <div class="team">
                    <div class="team-flag">
                    <img src="vidyartha.png" alt="" width="50" height="50" style="border-radius: 50%;">

                    </div>
                    <div class="team-name"><?php echo $team2['team_name']; ?></div>
                    <div class="team-score">
                        <?php if ($batting_first_id == $team2['team_id']): ?>
                            <?php echo $first_innings['runs'] . '/' . $first_innings['wickets'] . ' (' . $first_innings['overs'] . ' ov)' ?>
                        <?php elseif (isset($second_innings) && $bowling_first_id == $team2['team_id']): ?>
                            <?php echo $second_innings['runs'] . '/' . $second_innings['wickets'] . ' (' . $second_innings['overs'] . ' ov)' ?>
                        <?php else: ?>
                            Yet to bat
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div style="text-align: center;">
                <span class="match-status">
                    <div class="live-indicator">
                        <span class="pulse"></span>
                        LIVE
                    </div>
                </span>
            </div>
            <div class="match-meta">
                <div class="match-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Pallekele International Cricket Stadium</span>
                </div>
                <div class="match-meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?php echo date('M j, Y - g:i A', strtotime($match['match_date'])) ?></span>
                </div>
                <div class="match-meta-item">
                    <i class="fas fa-flag"></i>
                    <span>
                        Toss: <?php echo $team1['team_id'] == $toss_winner_id ? $team1['team_name'] : $team2['team_name']; ?> won the toss and chose to 
                        <?php echo ucfirst($toss_decision); ?>.
                    </span>
                </div>
                <div class="match-meta-item">
                    <i class="fas fa-info-circle"></i>
                    <span><?php echo $match_status ?></span>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column">
            <!-- Main Column -->
            <div class="main-column">
                <?php if (isset($second_innings)): ?>
                    <!-- Show second innings scorecard first if it exists -->
                    <div class="scorecard">
                        <div class="scorecard-header">
                            <div class="scorecard-title">
                                <i class="fas fa-bat"></i> <?php echo ($bowling_first_id == $team1['team_id']) ? $team1['team_name'] : $team2['team_name'] ?> Batting
                            </div>
                            <div class="last-updated"></div>
                            <button id="refreshScoreBtn" style="background: var(--secondary); color: #fff; border: none; padding: 7px 18px; border-radius: 5px; font-size: 0.95rem; cursor: pointer; margin-left: 15px;">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="scorecard-summary">
                            <div class="summary-item">
                                <div class="summary-label">Score</div>
                                <div class="summary-value"><?php echo $second_innings['runs'] . '/' . $second_innings['wickets'] ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Overs</div>
                                <div class="summary-value"><?php echo $second_innings['overs'] ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Extras</div>
                                <div class="summary-value"><?php echo $second_innings_bowling['extras'] ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Run Rate</div>
                                <div class="summary-value"><?php echo $second_innings['run_rate'] ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Target</div>
                                <div class="summary-value"><?php echo $first_innings['runs'] + 1 ?></div>
                            </div>
                        </div>
                        <div class="scorecard-body">
                            <div class="table-responsive">
                                <table class="scorecard-table">
                                    <thead>
                                        <tr>
                                            <th>Batsman</th>
                                            <th>Status</th>
                                            <th>R</th>
                                            <th>B</th>
                                            <th>4s</th>
                                            <th>6s</th>
                                            <th>SR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($second_innings['players'] as $row): ?>
                                            <?php
                                            $sr = ($row['balls_faced'] > 0) ? round(($row['runs'] / $row['balls_faced']) * 100, 2) : 0;
                                            if ($row['is_out']) {
                                                $dismissal_status = '<span class="status status-out">Out</span>';
                                            } else {
                                                $dismissal_status = '<span class="status status-notout">Not Out</span>';
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="player-stats">
                                                        <span><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($row['is_out']) {
                                                        echo $row['dismissal_status'];
                                                    }
                                                    echo $dismissal_status;
                                                    if ($row['is_out']) {
                                                        echo '<div class="dismissal-details"><strong>' . ucfirst(htmlspecialchars($row['dismissal_type'])) . '</strong></div>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo (int)$row['runs'] ?></td>
                                                <td><?php echo (int)$row['balls_faced'] ?></td>
                                                <td><?php echo (int)$row['fours'] ?></td>
                                                <td><?php echo (int)$row['sixes'] ?></td>
                                                <td><?php echo $sr ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="scorecard-footer">
                            <div><strong>Yet to bat:</strong> <?php echo $second_innings['yet_to_bat'] ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- First innings scorecard -->
                <div class="scorecard">
                    <div class="scorecard-header">
                        <div class="scorecard-title">
                            <i class="fas fa-bat"></i> <?php echo ($batting_first_id == $team1['team_id']) ? $team1['team_name'] : $team2['team_name'] ?> Batting
                        </div>
                        <div class="last-updated"></div>
                        <?php if (!isset($second_innings)): ?>
                            <button id="refreshScoreBtn" style="background: var(--secondary); color: #fff; border: none; padding: 7px 18px; border-radius: 5px; font-size: 0.95rem; cursor: pointer; margin-left: 15px;">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="scorecard-summary">
                        <div class="summary-item">
                            <div class="summary-label">Score</div>
                            <div class="summary-value"><?php echo $first_innings['runs'] . '/' . $first_innings['wickets'] ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Overs</div>
                            <div class="summary-value"><?php echo $first_innings['overs'] ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Extras</div>
                            <div class="summary-value"><?php echo $first_innings_bowling['extras'] ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Run Rate</div>
                            <div class="summary-value"><?php echo $first_innings['run_rate'] ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Projected</div>
                            <div class="summary-value"><?php echo round($first_innings['run_rate'] * 20) ?></div>
                        </div>
                    </div>
                    <div class="scorecard-body">
                        <div class="table-responsive">
                            <table class="scorecard-table">
                                <thead>
                                    <tr>
                                        <th>Batsman</th>
                                        <th>Status</th>
                                        <th>R</th>
                                        <th>B</th>
                                        <th>4s</th>
                                        <th>6s</th>
                                        <th>SR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($first_innings['players'] as $row): ?>
                                        <?php
                                        $sr = ($row['balls_faced'] > 0) ? round(($row['runs'] / $row['balls_faced']) * 100, 2) : 0;
                                        if ($row['is_out']) {
                                            $dismissal_status = '<span class="status status-out">Out</span>';
                                        } else {
                                            $dismissal_status = '<span class="status status-notout">Not Out</span>';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="player-stats">
                                                    <span><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                if ($row['is_out']) {
                                                    echo $row['dismissal_status'];
                                                }
                                                echo $dismissal_status;
                                                if ($row['is_out']) {
                                                    echo '<div class="dismissal-details"><strong>' . ucfirst(htmlspecialchars($row['dismissal_type'])) . '</strong></div>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo (int)$row['runs'] ?></td>
                                            <td><?php echo (int)$row['balls_faced'] ?></td>
                                            <td><?php echo (int)$row['fours'] ?></td>
                                            <td><?php echo (int)$row['sixes'] ?></td>
                                            <td><?php echo $sr ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="scorecard-footer">
                        <div><strong>Yet to bat:</strong> <?php echo $first_innings['yet_to_bat'] ?></div>
                    </div>
                </div>
            </div>

            <!-- Side Column -->
            <div class="side-column">
                <!-- Current Over -->
                <!-- Keep the same current over section if needed -->

                <!-- Bowling Card -->
                <div class="scorecard">
                    <div class="scorecard-header">
                        <div class="scorecard-title">
                            <?php echo (isset($second_innings) ? $team1['team_name'] : $team2['team_name']) ?> Bowling
                        </div>
                    </div>
                    <div class="scorecard-body">
                        <div class="table-responsive">
                            <table class="scorecard-table">
                                <thead>
                                    <tr>
                                        <th>Bowler</th>
                                        <th>O</th>
                                        <th>M</th>
                                        <th>R</th>
                                        <th>W</th>
                                        <th>Econ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $bowling_figures = isset($second_innings) ? $second_innings_bowling['rows'] : $first_innings_bowling['rows'];
                                    foreach ($bowling_figures as $row):
                                        $economy = ($row['overs'] > 0) ? round($row['runs_conceded'] / $row['overs'], 2) : 0;
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                            <td><?php echo (double)$row['overs'] ?></td>
                                            <td><?php echo (int)$row['maidens'] ?></td>
                                            <td><?php echo (int)$row['runs_conceded'] ?></td>
                                            <td><?php echo (int)$row['wickets'] ?></td>
                                            <td><?php echo $economy ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php if (isset($second_innings)): ?>
                    <!-- Show first innings bowling if second innings is in progress -->
                    <div class="scorecard" style="margin-top: 20px;">
                        <div class="scorecard-header">
                            <div class="scorecard-title">
                                <?php echo ($batting_first_id == $team1['team_id']) ? $team2['team_name'] : $team1['team_name'] ?> Bowling
                            </div>
                        </div>
                        <div class="scorecard-body">
                            <div class="table-responsive">
                                <table class="scorecard-table">
                                    <thead>
                                        <tr>
                                            <th>Bowler</th>
                                            <th>O</th>
                                            <th>M</th>
                                            <th>R</th>
                                            <th>W</th>
                                            <th>Econ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($first_innings_bowling['rows'] as $row): ?>
                                            <?php $economy = ($row['overs'] > 0) ? round($row['runs_conceded'] / $row['overs'], 2) : 0; ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                                <td><?php echo (double)$row['overs'] ?></td>
                                                <td><?php echo (int)$row['maidens'] ?></td>
                                                <td><?php echo (int)$row['runs_conceded'] ?></td>
                                                <td><?php echo (int)$row['wickets'] ?></td>
                                                <td><?php echo $economy ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('refreshScoreBtn').onclick = function() {
            window.location.reload();
        };
    </script>
</body>
</html>