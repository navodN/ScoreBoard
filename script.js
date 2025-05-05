function toggleTeam(teamId, event) {
    const team1 = document.getElementById('team1');
    const team2 = document.getElementById('team2');

    if (teamId === 'team1') {
      team1.style.display = 'block';
      setTimeout(() => team1.style.opacity = 1, 50);
      team2.style.opacity = 0;
      setTimeout(() => team2.style.display = 'none', 400);
    } else {
      team2.style.display = 'block';
      setTimeout(() => team2.style.opacity = 1, 50);
      team1.style.opacity = 0;
      setTimeout(() => team1.style.display = 'none', 400);
    }

    const buttons = document.querySelectorAll('.toggle-buttons button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function saveScore() {
    const form = document.getElementById('battingScoreForm');
    const formData = new FormData(form);

    // Add additional fields to the form data
    formData.append('playerId', document.getElementById('player').value);
    formData.append('position', document.getElementById('position').value);
    formData.append('runs', document.getElementById('runs').value);
    formData.append('balls', document.getElementById('balls').value);
    formData.append('fours', document.getElementById('fours').value);
    formData.append('sixes', document.getElementById('sixes').value);
    formData.append('isOut', document.getElementById('isOut').value);
    formData.append('dismissalType', document.getElementById('dismissalType').value);
    formData.append('bowledBy', document.getElementById('bowledBy').value);
    formData.append('caughtBy', document.getElementById('caughtBy').value);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ssckBatProcess.php', true);

    xhr.onload = function () {
      if (xhr.status === 200 && xhr.readyState === 4) {
        if (xhr.responseText === 'success') {
          alert('Score saved successfully!');
          resetForm();
          toggleScoreView(); // Refresh the scorecard
        }else{
          alert('Error saving score: ' + xhr.responseText);
        }
      } else {
        alert('An error occurred: ' + xhr.statusText);
      }
    };

    xhr.onerror = function () {
      alert('An error occurred during the request.');
    };

    xhr.send(formData);
}
