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

function saveScore2() {
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
    xhr.open('POST', 'vckBatProcess.php', true);

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

function saveBowlingFigures() {
    const form = document.getElementById('bowlingFiguresForm');
    const formData = new FormData(form);

    // Add additional fields to the form data
    formData.append('bowlerId', document.getElementById('bowler').value);
    formData.append('bowlingOrder', document.getElementById('bowlingOrder').value);
    formData.append('overs', document.getElementById('overs').value);
    formData.append('maidens', document.getElementById('maidens').value);
    formData.append('runsConceded', document.getElementById('runsConceded').value);
    formData.append('wickets', document.getElementById('wickets').value);
    formData.append('wides', document.getElementById('wides').value);
    formData.append('noBalls', document.getElementById('noballs').value);
    formData.append('dots', document.getElementById('dots').value);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'ssckVCKBallProcess.php', true);

    xhr.onload = function () {
        if (xhr.status === 200 && xhr.readyState === 4) {
          if (xhr.responseText === 'success') {
            alert('Figure saved successfully!');
            resetForm();
            toggleScoreView(); // Refresh the scorecard
          }else{
            alert('Error Figure score: ' + xhr.responseText);
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

function saveBowlingFigures() {
    const form = document.getElementById('bowlingFiguresForm');
    const formData = new FormData(form);

    // Add additional fields to the form data
    formData.append('bowlerId', document.getElementById('bowler').value);
    formData.append('bowlingOrder', document.getElementById('bowlingOrder').value);
    formData.append('overs', document.getElementById('overs').value);
    formData.append('maidens', document.getElementById('maidens').value);
    formData.append('runsConceded', document.getElementById('runsConceded').value);
    formData.append('wickets', document.getElementById('wickets').value);
    formData.append('wides', document.getElementById('wides').value);
    formData.append('noBalls', document.getElementById('noballs').value);
    formData.append('dots', document.getElementById('dots').value);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'vckSSCKBallProcess.php', true);

    xhr.onload = function () {
        if (xhr.status === 200 && xhr.readyState === 4) {
          if (xhr.responseText === 'success') {
            alert('Figure saved successfully!');
            resetForm();
            toggleScoreView(); // Refresh the scorecard
          }else{
            alert('Error Figure score: ' + xhr.responseText);
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

function resetBowlingForm() {
    document.getElementById('bowlingFiguresForm').reset();
}

function toggleBowlingView() {
    window.location.reload(); // Reload the page to refresh the bowling figures
}

function deleteScore(playerId) {
    if (!confirm('Are you sure you want to delete this record?')) {
        return;
    }

    const formData = new FormData();
    formData.append('playerId', playerId);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'deleteRec.php', true);

    xhr.onload = function () {
        if (xhr.status === 200 && xhr.readyState === 4) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(response.message);
                toggleScoreView(); // Refresh the scorecard
            } else {
                alert('Error deleting record: ' + response.message);
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

function deleteBowlingFigure(playerId) {
    if (!confirm('Are you sure you want to delete this figure?')) {
        return;
    }

    const formData = new FormData();
    formData.append('playerId', playerId);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'deleteFigure.php', true);

    xhr.onload = function () {
        if (xhr.status === 200 && xhr.readyState === 4) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(response.message);
                toggleScoreView(); // Refresh the scorecard
            } else {
                alert('Error deleting record: ' + response.message);
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