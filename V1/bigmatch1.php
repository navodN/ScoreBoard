<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>36th Battle of Babes</title>
  <link rel="stylesheet" href="style.css">

</head>

<body>
  <header>
    <h1>36th Battle of Babes</h1>
    <h3>St. Sylvester's College vs Vidyartha College <span class="live-badge">LIVE</span></h3>
  </header>

  <div class="toss-info">
    <p><strong>Toss:</strong> St. Sylvester's College won the toss and elected to bat first</p>
    <p><strong>Venue:</strong> Pallekele International Cricket Stadium</p>
  </div>

  <div class="toggle-buttons">
    <button class="active" onclick="toggleTeam('team1', event)">Royal College</button>
    <button onclick="toggleTeam('team2', event)">St. Thomas' College</button>
  </div>

  <div id="team1" class="scorecard">
    <div class="match-summary">
      <div class="summary-card" style="flex: 2; border-left: 4px solid var(--primary-color);">
        <h4>Current Batters</h4>
        <p>Rhys Mariu <br><small>58 (61) · SR 95.08</small></p>
        <p style="color: #5c5c5c;">Nick Kelly <br><small>3 (9) · SR 33.33</small></p>
      </div>
      <div class="summary-card" style="flex: 1; border-left: 4px solid var(--primary-color);">
        <h4>Bowling Now</h4>
        <p>Naseem Shah <br>9-0-54-2 · Econ 8.00</p>
      </div>
    </div>

    <div class="match-summary">
      <div class="summary-card">
        <h4>Total Runs</h4>
        <p>264/8 <br><small>(42 Overs)</small></p>
      </div>
      <div class="summary-card">
        <h4>Extras</h4>
        <p>20 <br><small>(NB 2, W 9, B 4, LB 5)</small></p>
      </div>
      <div class="summary-card">
        <h4>Partnership</h4>
        <p>37 (28)</p>
      </div>
      <div class="summary-card">
        <h4>Last 5 Overs</h4>
        <p>47/2</p>
      </div>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Batter</th>
            <th>Runs</th>
            <th>Balls</th>
            <th>Dismissal</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>John Perera</td>
            <td>12</td>
            <td>18</td>
            <td>c slip b Shah</td>
          </tr>
          <tr>
            <td>Asela Gunaratne</td>
            <td>45</td>
            <td>50</td>
            <td>b Akif</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Bowler</th>
            <th>Overs</th>
            <th>Maidens</th>
            <th>Runs</th>
            <th>Wickets</th>
            <th>Economy</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Naseem Shah</td>
            <td>9</td>
            <td>0</td>
            <td>54</td>
            <td>2</td>
            <td>8.00</td>
          </tr>
          <tr>
            <td>Akif Javed</td>
            <td>8</td>
            <td>1</td>
            <td>82</td>
            <td>4</td>
            <td>7.75</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="team2" class="scorecard">
    <p style="text-align:center;">Waiting for 2nd innings update...</p>
  </div>

  <script src="script.js"></script>
</body>

</html>