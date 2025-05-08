<?php

include "connection.php";

$ssck = Database::getValue("SELECT * FROM `teams` WHERE `team_id` = 1");
$vck = Database::getValue("SELECT * FROM `teams` WHERE `team_id` = 2");

// Fetch SSCK players who have batted (is_batted = 1)
$batting_result = Database::q("SELECT p.*, b.* 
FROM players p 
LEFT JOIN batting_scores b ON p.player_id = b.player_id 
WHERE p.team_id = 1 AND b.is_batted = 1 
ORDER BY b.position ASC");

$batting_data = mysqli_fetch_assoc($batting_result);

// Calculate batting totals for SSCK
$total_runs = 0;
$total_balls = 0;
$total_fours = 0;
$total_sixes = 0;
$total_wickets = 0;

mysqli_data_seek($batting_result, 0); // Reset pointer
while ($row = mysqli_fetch_assoc($batting_result)) {
    $total_runs += (int)$row['runs'];
    $total_balls += (int)$row['balls_faced'];
    $total_fours += (int)$row['fours'];
    $total_sixes += (int)$row['sixes'];
    if ($row['is_out']) {
        $total_wickets++;
    }
}

$strike_rate = ($total_balls > 0) ? round(($total_runs / $total_balls) * 100, 2) : 0;

// Calculate run rate
$run_rate = ($total_balls > 0) ? round($total_runs / ($total_balls / 6), 2) : 0;

// Fetch SSCK players who have not batted yet
$yet_to_bat_result = Database::q("SELECT first_name, last_name FROM players p 
    LEFT JOIN batting_scores b ON p.player_id = b.player_id 
    WHERE p.team_id = 1 AND (b.is_batted = 0 OR b.is_batted IS NULL)");

$yet_to_bat_names = [];
while ($row = mysqli_fetch_assoc($yet_to_bat_result)) {
    $yet_to_bat_names[] = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
}
$yet_to_bat = !empty($yet_to_bat_names) ? implode(', ', $yet_to_bat_names) : 'None';

// Fetch bowling data for team_id = 2 (Vidyartha)
$bowling_result = Database::q("SELECT p.*, b.* 
FROM players p 
LEFT JOIN bowling_figures b ON p.player_id = b.player_id 
WHERE p.team_id = 2 AND b.is_bowled = 1 
ORDER BY b.overs ASC");

// Store all bowling rows in an array for reuse
$bowling_rows = [];
$total_overs = 0;
$extras = 0;

// Calculate total overs and extras for SSCK from bowling figures of Vidyartha (team_id = 2)
$total_balls = 0;
$extras = 0;
while ($row = mysqli_fetch_assoc($bowling_result)) {
    $bowling_rows[] = $row;
    // overs may be in decimal (e.g., 4.2 overs means 4 overs and 2 balls)
    $overs_parts = explode('.', $row['overs']);
    $whole_overs = isset($overs_parts[0]) ? (int)$overs_parts[0] : 0;
    $balls = isset($overs_parts[1]) ? (int)$overs_parts[1] : 0;
    $total_balls += ($whole_overs * 6) + $balls;

    $extras += (isset($row['wides']) ? (int)$row['wides'] : 0) + (isset($row['no_balls']) ? (int)$row['no_balls'] : 0);
}
$total_overs = floor($total_balls / 6) . '.' . ($total_balls % 6);

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
    <!-- Header -->
    <!-- <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-cricket-bat-ball"></i>
                </div>
                <nav class="nav-links">
                    <a href="#" class="active">Live Matches</a>
                    <a href="#">Schedule</a>
                    <a href="#">Teams</a>
                    <a href="#">Stats</a>
                    <a href="#">News</a>
                </nav>
            </div>
        </div>
    </header> -->

    <div class="container">
        <!-- Match Banner -->
        <div class="match-banner">
            <h1 class="match-title">St. Sylvester's Vs. Vidyartha - Battle of the Babes</h1>
            <div class="match-teams">
                <div class="team">
                    <div class="team-flag">
                        <img src="ssck.png" alt="" width="30" height="50">
                    </div>
                    <div class="team-name">St. Sylvester's</div>
                    <div class="team-score"><?php echo $total_runs . '/' . $total_wickets . ' (' . $total_overs . ' ov)'; ?></div>
                </div>
                <div class="versus">VS</div>
                <div class="team">
                    <div class="team-flag">
                        <img src="vck.jpeg" alt="" width="50" height="50" style="border-radius: 50%;">
                    </div>
                    <div class="team-name">Vidyartha</div>
                    <div class="team-score">Yet to bat</div>
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
                    <span>May 9, 2025 - 9:00 PM</span>
                </div>
                <div class="match-meta-item">
                    <i class="fas fa-info-circle"></i>
                    <span>1st Innings in progress</span>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column">
            <!-- Main Column -->
            <div class="main-column">
                <!-- Batting Scorecard -->
                <div class="scorecard">
                    <div class="scorecard-header">
                        <div class="scorecard-title">
                            <i class="fas fa-bat"></i> SSCK Batting
                        </div>
                        <div class="last-updated"></div>
                        <button id="refreshScoreBtn" style="background: var(--secondary); color: #fff; border: none; padding: 7px 18px; border-radius: 5px; font-size: 0.95rem; cursor: pointer; margin-left: 15px;">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <script>
                            document.getElementById('refreshScoreBtn').onclick = function() {
                                window.location.reload();
                                alert('Scorecard refreshed');
                            };
                        </script>
                    </div>
                    <div class="scorecard-summary">
                        <div class="summary-item">
                            <div class="summary-label">Score</div>
                            <div class="summary-value"><?php echo $total_runs . '/' . $total_wickets; ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Overs</div>
                            <div class="summary-value"><?php echo $total_overs; ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Extras</div>
                            <div class="summary-value"><?php echo $extras; ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Run Rate</div>
                            <div class="summary-value"><?php echo $run_rate; ?></div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Projected</div>
                            <div class="summary-value"><?php echo round($run_rate * 20); ?></div>
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
                                <tbody id="battingScorecard">
                                    <?php
                                    mysqli_data_seek($batting_result, 0); // Ensure pointer is at start
                                    while ($row = mysqli_fetch_assoc($batting_result)) {
                                        $sr = ($row['balls_faced'] > 0) ? round(($row['runs'] / $row['balls_faced']) * 100, 2) : 0;
                                        $player_id = $row['player_id'];
                                        if ($row['is_out']) {
                                            $dismissal_status = '<span class="status status-out">Out</span>';
                                        } else {
                                            $dismissal_status = '<span class="status status-notout">Not Out</span>';
                                        }
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="player-stats">
                                                    <span><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></span>
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
                                            <td><?php echo (int)$row['runs']; ?></td>
                                            <td><?php echo (int)$row['balls_faced']; ?></td>
                                            <td><?php echo (int)$row['fours']; ?></td>
                                            <td><?php echo (int)$row['sixes']; ?></td>
                                            <td><?php echo $sr; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="scorecard-footer">
                        <div><strong>Yet to bat:</strong> <?php echo $yet_to_bat; ?></div>
                    </div>
                </div>

            </div>

            <!-- Side Column -->
            <div class="side-column">
                <!-- Current Over -->
                <!-- <div class="current-over">
                    <div class="current-over-title">Current Over (17th) - Zampa bowling</div>
                    <div class="over-balls">
                        <div class="ball ball-dot">0</div>
                        <div class="ball ball-six">6</div>
                        <div class="ball ball-dot">•</div>
                        <div class="ball ball-dot">•</div>
                        <div class="ball ball-run">1</div>
                        <div class="ball ball-four">4</div>
                    </div>
                </div> -->

                <!-- Bowling Card -->
                <div class="scorecard">
                    <div class="scorecard-header">
                        <div class="scorecard-title">
                            VCK Bowling
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
                                    foreach ($bowling_rows as $row) {
                                        $economy = ($row['overs'] > 0) ? round($row['runs_conceded'] / $row['overs'], 2) : 0;
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>';
                                        echo '<td>' . (double)$row['overs'] . '</td>';
                                        echo '<td>' . (int)$row['maidens'] . '</td>';
                                        echo '<td>' . (int)$row['runs_conceded'] . '</td>';
                                        echo '<td>' . (int)$row['wickets'] . '</td>';
                                        echo '<td>' . $economy . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>

    </script>
</body>

</html>