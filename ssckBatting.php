<?php

include "connection.php";

$ssck = Database::q("SELECT * FROM `players` WHERE `team_id` = 1");
$vck = Database::q("SELECT * FROM `players` WHERE `team_id` = 2");

$players_ssck = mysqli_fetch_assoc($ssck);
$players_vck = mysqli_fetch_assoc($vck);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket Admin Panel - Batting Scores</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--primary);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 1.8rem;
            color: var(--dark);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.2rem;
            color: var(--dark);
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        /* Form Styles */
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #555;
        }

        .table tr:hover {
            background-color: #f8f9fa;
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

        /* Custom styles for batting scores */
        .match-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .match-info-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .match-info-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .match-info-item {
            flex: 1;
            min-width: 200px;
        }

        .match-info-label {
            font-weight: 500;
            color: #666;
            margin-bottom: 5px;
        }

        .match-info-value {
            font-size: 1.1rem;
        }

        .score-card {
            position: relative;
        }

        .toggle-view {
            cursor: pointer;
            color: var(--secondary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .player-stats {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .player-img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .dismissal-details {
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-cricket"></i> Cricket Admin</h3>
            </div>
            <nav class="nav-menu">
                <a href="#" class="menu-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-users"></i> Teams
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-user-alt"></i> Players
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-calendar-alt"></i> Matches
                </a>
                <a href="ssckBatting.php" class="menu-item active">
                    <i class="fas fa-chart-bar"></i> SSCK Batting
                </a>
                <a href="vckBatting.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i> VCK Batting
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-chart-line"></i> Statistics
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <h1 class="page-title">Batting Scorecard</h1>
                <div class="user-info">
                    <span>Admin User</span>
                    <div class="user-avatar">A</div>
                </div>
            </div>

            <!-- Match Info -->
            <div class="match-info">
                <h2 class="match-info-title">St. Sylvester's Vs. Vidyartha - Battle of the Babes</h2>
                <div class="match-info-details">
                    <div class="match-info-item">
                        <div class="match-info-label">Venue</div>
                        <div class="match-info-value">Pallekele International Cricket Stadium</div>
                    </div>
                    <div class="match-info-item">
                        <div class="match-info-label">Date</div>
                        <div class="match-info-value">May 9, 2025</div>
                    </div>
                    <div class="match-info-item">
                        <div class="match-info-label">Match Status</div>
                        <div class="match-info-value">Live</div>
                    </div>
                    <div class="match-info-item">
                        <div class="match-info-label">Current Innings</div>
                        <div class="match-info-value">1st Innings (SSCK Batting)</div>
                    </div>
                </div>
            </div>

            <!-- Add New Batting Score -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add Player Score</h3>
                </div>
                <div class="card-body">
                    <form id="battingScoreForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="player">Batsman</label>
                                <select id="player" class="form-control" required>
                                    <option value="">Select Batsman</option>

                                    <?php
                                    mysqli_data_seek($ssck, 0); // Reset the pointer to the beginning
                                    while ($players_ssck = mysqli_fetch_assoc($ssck)) {
                                    ?>
                                        <option value="<?php echo $players_ssck['player_id'] ?>"><?php echo $players_ssck['first_name'] . ' ' . $players_ssck['last_name'] ?></option>
                                    <?php
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="position">Batting Position</label>
                                <select id="position" class="form-control" required>
                                    <option value="">Select Position</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="runs">Runs</label>
                                <input type="number" id="runs" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="balls">Balls Faced</label>
                                <input type="number" id="balls" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="fours">4s</label>
                                <input type="number" id="fours" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="sixes">6s</label>
                                <input type="number" id="sixes" class="form-control" min="0" value="0" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="isOut">Dismissal Status</label>
                                <select id="isOut" class="form-control" required onchange="toggleDismissalFields()">
                                    <option value="false">Not Out</option>
                                    <option value="true">Out</option>
                                </select>
                            </div>
                            <div class="form-group dismissal-field" style="display: none;">
                                <label for="dismissalType">Dismissal Type</label>
                                <select id="dismissalType" class="form-control">
                                    <option value="">Select Dismissal Type</option>
                                    <option value="bowled">Bowled</option>
                                    <option value="caught">Caught</option>
                                    <option value="lbw">LBW</option>
                                    <option value="run_out">Run Out</option>
                                    <option value="stumped">Stumped</option>
                                    <option value="hit_wicket">Hit Wicket</option>
                                    <option value="retired_hurt">Retired Hurt</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row dismissal-field" style="display: none;">
                            <div class="form-group">
                                <label for="bowledBy">Bowled By</label>
                                <select id="bowledBy" class="form-control">
                                    <option value="">Select Bowler</option>
                                    <?php
                                    mysqli_data_seek($vck, 0); // Reset the pointer to the beginning   
                                    while ($players_vck = mysqli_fetch_assoc($vck)) {
                                        if (!empty($players_vck['bowling_style'])) { // Check if bowling_style is not null or empty
                                    ?>
                                            <option value="<?php echo $players_vck['player_id'] ?>"><?php echo $players_vck['first_name'] . ' ' . $players_vck['last_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group caught-field" style="display: none;">
                                <label for="caughtBy">Caught By</label>
                                <select id="caughtBy" class="form-control">
                                    <option value="">Select Fielder</option>
                                    <?php
                                    mysqli_data_seek($vck, 0); // Reset the pointer to the beginning
                                    while ($players_vck = mysqli_fetch_assoc($vck)) {
                                    ?>
                                        <option value="<?php echo $players_vck['player_id'] ?>"><?php echo $players_vck['first_name'] . ' ' . $players_vck['last_name'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="actions">
                            <button type="button" class="btn btn-danger" onclick="resetForm()">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveScore()">Save Score</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Batting Scorecard -->
            <div class="card score-card">
                <div class="card-header">
                    <h3 class="card-title">SSCK Batting Scorecard</h3>
                    <span class="toggle-view" onclick="toggleScoreView()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Batter</th>
                                    <th>Dismissal</th>
                                    <th>R</th>
                                    <th>B</th>
                                    <th>4s</th>
                                    <th>6s</th>
                                    <th>SR</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="battingScorecard">
                                <!-- Sample data - would be populated dynamically -->
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <span>Rohit Sharma</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>c Smith b Cummins</div>
                                        <div class="dismissal-details">Caught at first slip</div>
                                    </td>
                                    <td>45</td>
                                    <td>28</td>
                                    <td>4</td>
                                    <td>3</td>
                                    <td>160.71</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <span>KL Rahul</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>b Starc</div>
                                        <div class="dismissal-details">Bowled through the gate</div>
                                    </td>
                                    <td>23</td>
                                    <td>19</td>
                                    <td>3</td>
                                    <td>0</td>
                                    <td>121.05</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <span>Virat Kohli</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status status-notout">Not Out</span>
                                    </td>
                                    <td>67</td>
                                    <td>52</td>
                                    <td>7</td>
                                    <td>2</td>
                                    <td>128.85</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <span>Rishabh Pant</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status status-notout">Not Out</span>
                                    </td>
                                    <td>32</td>
                                    <td>18</td>
                                    <td>3</td>
                                    <td>2</td>
                                    <td>177.78</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td><strong>167/2</strong></td>
                                    <td><strong>117</strong></td>
                                    <td><strong>17</strong></td>
                                    <td><strong>7</strong></td>
                                    <td><strong>142.74</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="8">
                                        <div><strong>Yet to bat:</strong> Hardik Pandya, Ravindra Jadeja, Axar Patel, Bhuvneshwar Kumar, Jasprit Bumrah, Yuzvendra Chahal</div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add New Bowling Figures -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add Bowling Figures</h3>
                </div>
                <div class="card-body">
                    <form id="bowlingFiguresForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="bowler">Bowler</label>
                                <select id="bowler" class="form-control" required>
                                    <option value="">Select Bowler</option>
                                    <?php
                                    mysqli_data_seek($vck, 0); // Reset the pointer to the beginning   
                                    while ($players_vck = mysqli_fetch_assoc($vck)) {
                                        if (!empty($players_vck['bowling_style'])) { // Check if bowling_style is not null or empty
                                    ?>
                                            <option value="<?php echo $players_vck['player_id'] ?>"><?php echo $players_vck['first_name'] . ' ' . $players_vck['last_name'] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="bowlingOrder">Bowling Order</label>
                                <select id="bowlingOrder" class="form-control" required>
                                    <option value="">Select Order</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="overs">Overs</label>
                                <input type="number" id="overs" class="form-control" min="0" step="0.1" max="20" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="maidens">Maidens</label>
                                <input type="number" id="maidens" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="runsConceded">Runs Conceded</label>
                                <input type="number" id="runsConceded" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="wickets">Wickets</label>
                                <input type="number" id="wickets" class="form-control" min="0" value="0" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="wides">Wides</label>
                                <input type="number" id="wides" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="noballs">No Balls</label>
                                <input type="number" id="noballs" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="dots">Dot Balls</label>
                                <input type="number" id="dots" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="foursConceded">4s Conceded</label>
                                <input type="number" id="foursConceded" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="sixesConceded">6s Conceded</label>
                                <input type="number" id="sixesConceded" class="form-control" min="0" value="0" required>
                            </div>
                        </div>

                        <div class="actions">
                            <button type="button" class="btn btn-danger" onclick="resetBowlingForm()">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveBowlingFigures()">Save Figures</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Bowling Figures -->
            <div class="card score-card">
                <div class="card-header">
                    <h3 class="card-title">VCK Bowling Figures</h3>
                    <span class="toggle-view" onclick="toggleBowlingView()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Bowler</th>
                                    <th>O</th>
                                    <th>M</th>
                                    <th>R</th>
                                    <th>W</th>
                                    <th>WD</th>
                                    <th>NB</th>
                                    <th>Dots</th>
                                    <th>4s</th>
                                    <th>6s</th>
                                    <th>Econ</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bowlingScorecard">
                                <!-- Sample data - would be populated dynamically -->
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <div>
                                                <div>Mitchell Starc</div>
                                                <div class="bowling-type">Left-arm fast</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>3.0</td>
                                    <td>0</td>
                                    <td>28</td>
                                    <td>1</td>
                                    <td>2</td>
                                    <td>1</td>
                                    <td>9</td>
                                    <td>4</td>
                                    <td>1</td>
                                    <td>9.33</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <div>
                                                <div>Pat Cummins</div>
                                                <div class="bowling-type">Right-arm fast</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>4.0</td>
                                    <td>1</td>
                                    <td>32</td>
                                    <td>1</td>
                                    <td>1</td>
                                    <td>0</td>
                                    <td>12</td>
                                    <td>3</td>
                                    <td>2</td>
                                    <td>8.00</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <div>
                                                <div>Adam Zampa</div>
                                                <div class="bowling-type">Right-arm legbreak</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2.0</td>
                                    <td>0</td>
                                    <td>22</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>4</td>
                                    <td>2</td>
                                    <td>1</td>
                                    <td>11.00</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="player-stats">
                                            <img src="/api/placeholder/30/30" alt="Player" class="player-img">
                                            <div>
                                                <div>Nathan Lyon</div>
                                                <div class="bowling-type">Right-arm offbreak</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>3.0</td>
                                    <td>0</td>
                                    <td>25</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>8</td>
                                    <td>3</td>
                                    <td>1</td>
                                    <td>8.33</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"><strong>Total</strong></td>
                                    <td><strong>12.0</strong></td>
                                    <td><strong>0</strong></td>
                                    <td><strong>107</strong></td>
                                    <td><strong>2</strong></td>
                                    <td><strong>3</strong></td>
                                    <td><strong>1</strong></td>
                                    <td><strong>33</strong></td>
                                    <td><strong>12</strong></td>
                                    <td><strong>5</strong></td>
                                    <td><strong>8.92</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Toggle dismissal fields based on Out/Not Out selection
        function toggleDismissalFields() {
            const isOut = document.getElementById('isOut').value === 'true';
            const dismissalFields = document.querySelectorAll('.dismissal-field');

            dismissalFields.forEach(field => {
                field.style.display = isOut ? 'block' : 'none';
            });

            // Additional logic for caught dismissal
            const dismissalType = document.getElementById('dismissalType').value;
            const caughtField = document.querySelector('.caught-field');

            if (isOut && dismissalType === 'caught') {
                caughtField.style.display = 'block';
            } else {
                caughtField.style.display = 'none';
            }
        }

        // Handle dismissal type change
        document.getElementById('dismissalType').addEventListener('change', function() {
            const dismissalType = this.value;
            const caughtField = document.querySelector('.caught-field');

            if (dismissalType === 'caught') {
                caughtField.style.display = 'block';
            } else {
                caughtField.style.display = 'none';
            }
        });

        // Reset form
        function resetForm() {
            document.getElementById('battingScoreForm').reset();
            toggleDismissalFields();
        }

        // Save score (placeholder for backend integration)
        // function saveScore() {
        //     // Form validation
        //     const form = document.getElementById('battingScoreForm');
        //     const player = document.getElementById('player').value;
        //     const position = document.getElementById('position').value;

        //     if (!player || !position) {
        //         alert('Please select player and batting position');
        //         return;
        //     }

        //     // Here you would typically send an AJAX request to your backend
        //     alert('Score saved successfully!');

        //     // Reset form after saving
        //     resetForm();
        // }

        // Toggle scorecard view (placeholder function)
        function toggleScoreView() {
            // This would typically refresh the data from the server
            alert('Scorecard refreshed');
        }
    </script>
</body>

</html>