<?php
session_start();
require "db.php";

// Only accessible to trainers and owners
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'customer') {
    header("Location: dashboard.php");
    exit;
}

// Fetch all members for dropdown
$members = $pdo->query("SELECT id, full_name FROM customers ORDER BY full_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Membership Validator - Gym Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .validator-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: #111;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.7);
        }

        .validator-container h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #ccc;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group select, .form-group input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 10px;
            border: none;
            background: #222;
            color: white;
            font-size: 16px;
        }

        .form-group select:focus, .form-group input:focus {
            outline: none;
            box-shadow: 0 0 0 2px orange;
        }

        .btn-validate {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, orange, darkorange);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-validate:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(255,165,0,0.4);
        }

        .result-box {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            display: none;
        }

        .result-valid {
            background: rgba(46, 125, 50, 0.3);
            border: 2px solid #4caf50;
        }

        .result-invalid {
            background: rgba(211, 47, 47, 0.3);
            border: 2px solid #f44336;
        }

        .result-box h3 {
            margin-bottom: 15px;
        }

        .result-valid h3 {
            color: #4caf50;
        }

        .result-invalid h3 {
            color: #f44336;
        }

        .result-box p {
            color: #ccc;
            margin: 8px 0;
        }

        .result-box .warning {
            color: #ff9800;
            font-weight: bold;
            margin-top: 15px;
        }

        .loading {
            text-align: center;
            color: #ccc;
            display: none;
            margin-top: 20px;
        }

        .member-info {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .member-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navdiv">
        <div class="logo"><a href="index.php">Gym</a></div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="membership_validator.php">Membership Validator</a></li>
        </ul>
    </div>
</nav>

<div class="main-content">
    <h1 class="page-title">Membership Validator</h1>
    
    <div class="validator-container">
        <h2>Check Member Status</h2>
        
        <div class="form-group">
            <label for="member_select">Select Member:</label>
            <select id="member_select">
                <option value="">Choose a member...</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?= $member['id'] ?>">
                        <?= htmlspecialchars($member['full_name']) ?> (ID: <?= $member['id'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="member_id">Or Enter Member ID:</label>
            <input type="number" id="member_id" placeholder="Enter member ID">
        </div>
        
        <button class="btn-validate" onclick="validateMembership()">Validate Membership</button>
        
        <div class="loading" id="loading">
            <p>Validating membership...</p>
        </div>
        
        <div class="result-box" id="result"></div>
    </div>
</div>

<script>
// Update member_id input when dropdown changes
document.getElementById('member_select').addEventListener('change', function() {
    document.getElementById('member_id').value = this.value;
});

// Update dropdown when member_id input changes
document.getElementById('member_id').addEventListener('input', function() {
    document.getElementById('member_select').value = this.value;
});

function validateMembership() {
    const memberId = document.getElementById('member_id').value;
    const resultBox = document.getElementById('result');
    const loading = document.getElementById('loading');
    
    if (!memberId) {
        alert('Please select or enter a member ID');
        return;
    }
    
    // Show loading
    loading.style.display = 'block';
    resultBox.style.display = 'none';
    
    // Make Ajax request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'validate_membership.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        loading.style.display = 'none';
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                displayResult(response);
            } catch (e) {
                alert('Error parsing response');
            }
        } else {
            alert('Error validating membership');
        }
    };
    
    xhr.onerror = function() {
        loading.style.display = 'none';
        alert('Network error');
    };
    
    xhr.send('member_id=' + encodeURIComponent(memberId));
}

function displayResult(data) {
    const resultBox = document.getElementById('result');
    
    if (data.valid) {
        resultBox.className = 'result-box result-valid';
        resultBox.innerHTML = `
            <h3>✓ Valid Membership</h3>
            <div class="member-info">
                <p><strong>Member:</strong> ${data.member_name}</p>
                <p><strong>Type:</strong> ${data.membership_type}</p>
                <p><strong>Expires:</strong> ${data.expiry_date}</p>
                <p><strong>Status:</strong> ${data.message}</p>
            </div>
            ${data.warning ? '<p class="warning">⚠ ' + data.warning + '</p>' : ''}
        `;
    } else {
        resultBox.className = 'result-box result-invalid';
        resultBox.innerHTML = `
            <h3>✗ Invalid Membership</h3>
            <div class="member-info">
                ${data.member_name ? '<p><strong>Member:</strong> ' + data.member_name + '</p>' : ''}
                ${data.membership_type ? '<p><strong>Type:</strong> ' + data.membership_type + '</p>' : ''}
                ${data.expiry_date ? '<p><strong>Expired:</strong> ' + data.expiry_date + '</p>' : ''}
                <p><strong>Message:</strong> ${data.message}</p>
            </div>
        `;
    }
    
    resultBox.style.display = 'block';
}

// Allow Enter key to submit
document.getElementById('member_id').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        validateMembership();
    }
});
</script>

</body>
</html>