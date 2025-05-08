<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Score Update</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
    }
    button {
      background-color: #4CAF50;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background-color: #45a049;
    }
    .tabs {
      display: flex;
      margin-bottom: 20px;
    }
    .tab {
      padding: 10px 20px;
      cursor: pointer;
      background-color: #f1f1f1;
      margin-right: 5px;
    }
    .tab.active {
      background-color: #ddd;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
  </style>
</head>
<body>
  <h1>Admin Score Update Panel</h1>
  
  <div class="tabs">
    <div class="tab active" onclick="openTab('batting')">Batting Update</div>
    <div class="tab" onclick="openTab('bowling')">Bowling Update</div>
    <div class="tab" onclick="openTab('match')">Match Info</div>
  </div>
  
  <!-- Batting Update Tab -->
  <div id="batting" class="tab-content active">
    <h2>Update Batting Stats</h2>
    <form action="update_score.php" method="post">
      <div class="form-group">
        <label for="batter_name">Batter Name:</label>
        <input type="text" id="batter_name" name="batter_name" required>
      </div>
      
      <div class="form-group">
        <label for="runs">Runs:</label>
        <input type="number" id="runs" name="runs" required>
      </div>
      
      <div class="form-group">
        <label for="balls">Balls Faced:</label>
        <input type="number" id="balls" name="balls" required>
      </div>
      
      <div class="form-group">
        <label for="status">Status:</label>
        <select id="status" name="status">
          <option value="batting">Currently Batting</option>
          <option value="out">Out</option>
        </select>
      </div>
      
      <div class="form-group" id="dismissal-group" style="display:none;">
        <label for="dismissal">Dismissal:</label>
        <input type="text" id="dismissal" name="dismissal" placeholder="e.g., c slip b Shah">
      </div>
      
      <button type="submit" name="update_batting">Update Batting</button>
    </form>
  </div>
  
  <!-- Bowling Update Tab -->
  <div id="bowling" class="tab-content">
    <h2>Update Bowling Stats</h2>
    <form action="update_score.php" method="post">
      <div class="form-group">
        <label for="bowler_name">Bowler Name:</label>
        <input type="text" id="bowler_name" name="bowler_name" required>
      </div>
      
      <div class="form-group">
        <label for="overs">Overs Bowled:</label>
        <input type="number" step="0.1" id="overs" name="overs" required>
      </div>
      
      <div class="form-group">
        <label for="maidens">Maidens:</label>
        <input type="number" id="maidens" name="maidens" required>
      </div>
      
      <div class="form-group">
        <label for="runs_given">Runs Given:</label>
        <input type="number" id="runs_given" name="runs_given" required>
      </div>
      
      <div class="form-group">
        <label for="wickets">Wickets:</label>
        <input type="number" id="wickets" name="wickets" required>
      </div>
      
      <button type="submit" name="update_bowling">Update Bowling</button>
    </form>
  </div>
  
  <!-- Match Info Tab -->
  <div id="match" class="tab-content">
    <h2>Update Match Information</h2>
    <form action="update_score.php" method="post">
      <div class="form-group">
        <label for="total_runs">Total Runs:</label>
        <input type="number" id="total_runs" name="total_runs" required>
      </div>
      
      <div class="form-group">
        <label for="wickets">Wickets:</label>
        <input type="number" id="wickets" name="wickets" required>
      </div>
      
      <div class="form-group">
        <label for="overs">Overs:</label>
        <input type="number" step="0.1" id="overs" name="overs" required>
      </div>
      
      <div class="form-group">
        <label for="extras">Extras:</label>
        <input type="number" id="extras" name="extras" required>
      </div>
      
      <div class="form-group">
        <label for="partnership">Partnership:</label>
        <input type="text" id="partnership" name="partnership" placeholder="e.g., 37 (28)" required>
      </div>
      
      <div class="form-group">
        <label for="last_5_overs">Last 5 Overs:</label>
        <input type="text" id="last_5_overs" name="last_5_overs" placeholder="e.g., 47/2" required>
      </div>
      
      <button type="submit" name="update_match">Update Match Info</button>
    </form>
  </div>
  
  <script>
    function openTab(tabName) {
      // Hide all tab content
      const tabContents = document.getElementsByClassName('tab-content');
      for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
      }
      
      // Remove active class from all tabs
      const tabs = document.getElementsByClassName('tab');
      for (let i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
      }
      
      // Show the current tab and mark button as active
      document.getElementById(tabName).classList.add('active');
      event.currentTarget.classList.add('active');
    }
    
    // Show/hide dismissal field based on status
    document.getElementById('status').addEventListener('change', function() {
      const dismissalGroup = document.getElementById('dismissal-group');
      if (this.value === 'out') {
        dismissalGroup.style.display = 'block';
      } else {
        dismissalGroup.style.display = 'none';
      }
    });
  </script>
</body>
</html>