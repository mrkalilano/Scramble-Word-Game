<?php
session_start();

// Database connection configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'root';
$db_name = 'scramble_word';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$categories = [
    "Animals" => ["tiger", "elephant", "giraffe", "lion", "zebra", "panda", "kangaroo", "monkey", "rabbit", "dolphin"],
    "Fruits" => ["apple", "banana", "cherry", "grape", "orange", "peach", "pear", "plum", "strawberry", "watermelon"],
    "Vegetables" => ["carrot", "spinach", "potato", "onion", "broccoli", "pepper", "cucumber", "lettuce", "tomato", "garlic"],
    "Countries" => ["canada", "germany", "brazil", "india", "france", "italy", "japan", "china", "mexico", "spain"],
    "Sports" => ["soccer", "cricket", "tennis", "baseball", "hockey", "golf", "swimming", "volleyball", "rugby", "badminton"],
    "Colors" => ["orange", "purple", "red", "blue", "green", "yellow", "pink", "black", "white", "brown"],
    "Clothing" => ["jacket", "sweater", "scarf", "shirt", "pants", "dress", "shoes", "hat", "belt", "socks"],
    "Vehicles" => ["bicycle", "airplane", "scooter", "train", "car", "truck", "ship", "submarine", "bus", "motorcycle"],
    "Technology" => ["computer", "smartphone", "tablet", "laptop", "router", "keyboard", "monitor", "printer", "mouse", "camera"],
    "Professions" => ["doctor", "engineer", "artist", "teacher", "chef", "pilot", "nurse", "lawyer", "firefighter", "police"],
    "Music" => ["guitar", "piano", "trumpet", "violin", "drum", "flute", "saxophone", "keyboard", "cello", "harp"],
    "Body Parts" => ["heart", "kidney", "stomach", "brain", "lungs", "eyes", "ears", "hands", "feet", "liver"],
    "Weather" => ["thunder", "rainbow", "hurricane", "storm", "fog", "wind", "cloud", "snow", "hail", "lightning"],
    "Space" => ["planet", "galaxy", "asteroid", "star", "comet", "moon", "sun", "nebula", "satellite", "orbit"],
    "Household" => ["table", "curtain", "mirror", "sofa", "bed", "lamp", "door", "window", "chair", "carpet"]
];

function scrambleWord($word) {
    $letters = str_split($word);
    shuffle($letters);
    return implode('', $letters);
}

if (!isset($_SESSION['category'])) {
    $_SESSION['category'] = "";
    $_SESSION['words'] = [];
    $_SESSION['currentIndex'] = 0;
    $_SESSION['currentWord'] = "";
    $_SESSION['scrambledWord'] = "";
    $_SESSION['score'] = 0;
    $_SESSION['strikes'] = 0;
    $_SESSION['completed'] = false;
    $_SESSION['player_avatar'] = "";
    $_SESSION['player_name'] = "";
}

if (isset($_GET['reset'])) {
    session_unset();
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player'])) {
    $_SESSION['player'] = $_POST['player'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle player name submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_name'])) {
    $_SESSION['player_name'] = htmlspecialchars(trim($_POST['player_name']));
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category']) && empty($_SESSION['category'])) {
    $selectedCategory = $_POST['category'];
    $_SESSION['category'] = $selectedCategory;
    $_SESSION['words'] = $categories[$selectedCategory];
    shuffle($_SESSION['words']);
    $_SESSION['currentIndex'] = 0;
    $_SESSION['currentWord'] = $_SESSION['words'][$_SESSION['currentIndex']];
    $_SESSION['scrambledWord'] = scrambleWord($_SESSION['currentWord']);
    $_SESSION['score'] = 0;
    $_SESSION['strikes'] = 0;
    $_SESSION['completed'] = false;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guess'])) {
    $guess = strtolower(trim($_POST['guess']));

    if ($guess === $_SESSION['currentWord']) {
        $_SESSION['score']++;
        $message = "Correct! Moving to the next word.";
        $_SESSION['strikes'] = 0;
        $_SESSION['currentIndex']++;

        if ($_SESSION['currentIndex'] >= count($_SESSION['words'])) {
            $_SESSION['completed'] = true;
            
            // Save score to database when game is completed
            $stmt = $conn->prepare("INSERT INTO scores (player_name, avatar, score, category) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $_SESSION['player_name'], $_SESSION['player_avatar'], $_SESSION['score'], $_SESSION['category']);
            $stmt->execute();
            $stmt->close();
        } else {
            $_SESSION['currentWord'] = $_SESSION['words'][$_SESSION['currentIndex']];
            $_SESSION['scrambledWord'] = scrambleWord($_SESSION['currentWord']);
        }
    } else {
        $_SESSION['strikes']++;
        if ($_SESSION['strikes'] >= 3) {
            $message = "Incorrect! You have reached the maximum strikes. Your score is: " . $_SESSION['score'];
            $_SESSION['completed'] = true;
            
            // Save score to database even when game ends due to strikes
            $stmt = $conn->prepare("INSERT INTO scores (player_name, avatar, score, category) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $_SESSION['player_name'], $_SESSION['player_avatar'], $_SESSION['score'], $_SESSION['category']);
            $stmt->execute();
            $stmt->close();
        } else {
            $message = "Incorrect! Try again. Strikes: " . $_SESSION['strikes'] . "/3";
        }
    }
}
// Function to get leaderboard data
function getLeaderboard($conn) {
    $leaderboard = [];
    $query = "SELECT player_name, avatar, score, category, played_at 
              FROM scores 
              ORDER BY score DESC, played_at DESC 
              LIMIT 10";
    
    $result = $conn->query($query);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $leaderboard[] = $row;
        }
    }
    return $leaderboard;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scramble Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('images/background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
        }

        .welcome-container {
            text-align: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            color: white;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            width: 100%;
            max-width: 800px;
            margin: 20px auto 0;
            position: relative;
            z-index: 2;
        }

        .welcome-container h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .welcome-container p {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .select-prompt {
            color: #FFD700;
            font-weight: bold;
            margin-top: 20px;
            font-size: 1.3em !important;
        }

        .category-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            padding: 15px;
            max-width: 1000px;
            margin: 20px auto;
            position: relative;
            z-index: 1;
        }

        .player-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            width: 100%;
        }

        .category {
            width: 200px;
            text-align: center;
            cursor: pointer;
            border: 1px solid #00021d;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #00021d;
            padding: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .player-card {
            width: 150px;
            padding: 10px;
        }

        .player-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .player-card p {
            margin: 8px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .player-card button {
            width: 90%;
            padding: 6px 12px;
            font-size: 14px;
            background-color: #000000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .category:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .category img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            border:1px solid #91E9FF ;
            margin-bottom: 10px;
            object-fit: cover;
        }

        .category p {
            font-weight: bold;
            color: white;
            margin: 10px 0;
        }

        .category button {
            padding: 8px 16px;
            background-color: #004557;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 80%;
            transition: background-color 0.2s;
        }

        .category button:hover {
            background-color: #0056b3;
        }

        .game-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px auto;
            padding: 40px;
            background-color: #90a4ae;
            border-radius: 8px;
            width: 700px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .game-container:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .game-container p {
            margin: 30px 0;
            border-radius: 12px;
        }

        .game-container input {
            padding: 10px;
            width: 80%;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .game-container button {
            padding: 10px 20px;
            margin-top: 10px;
            background-color:#00021d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .game-container button:hover {
            background-color: #0056b3;
        }

        .scrambled-word-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
        }

        .letter {
            display: inline-block;
            font-size: 36px;
            font-weight: bold;
            background-color: #383838;
            color: white;
            padding: 10px;
            border-radius: 5px;
            transition: transform 0.2s ease-in-out;
            animation: bounce 0.6s ease forwards;
        }

        @keyframes bounce {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0);
            }
        }

        .letter:hover {
            transform: translateY(-5px);
        }

        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 40px;
            height: 40px;
            background-image: url('images/arrow.png');
            background-size: cover;
            background-position: center;
            border-radius: 50%;
            cursor: pointer;
            border: none;
        }

        .back-button:hover {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .player-card {
                width: 130px;
            }
            
            .player-card img {
                height: 100px;
            }
        }

        @media (max-width: 480px) {
            .category-container {
                padding: 10px;
                gap: 10px;
            }
            
            .player-card {
                width: 110px;
            }
            
            .player-card img {
                height: 90px;
            }
            
            .player-card p {
                font-size: 12px;
            }
            
            .player-card button {
                padding: 5px 10px;
                font-size: 12px;
            }

            .welcome-container {
                margin: 10px;
                padding: 15px;
            }

            .welcome-container h1 {
                font-size: 1.8em;
            }

            .welcome-container p {
                font-size: 1em;
            }
            .name-input-container {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 400px;
        }
        
        .leaderboard-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 800px;
        }
        
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .leaderboard-table th,
        .leaderboard-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .leaderboard-table th {
            background-color: #f5f5f5;
        }
        
        .player-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php if (empty($_SESSION['player'])): ?>
    <div class="welcome-container">
        <h1>Welcome to Word Scramble Game!</h1>
        <p class="select-prompt">PLEASE SELECT YOUR PLAYER TO BEGIN:</p>
    </div>
    <div class="category-container">
        <form action="" method="POST" class="player-grid">
            <?php
            $players = [
                ['name' => 'PLAYER 1', 'image' => 'image1.png'],
                ['name' => 'PLAYER 2', 'image' => 'image2.png'],
                ['name' => 'PLAYER 3', 'image' => 'image3.png'],
                ['name' => 'PLAYER 4', 'image' => 'image4.png'],
                ['name' => 'PLAYER 5', 'image' => 'image5.png']
            ];
            
            foreach ($players as $player): ?>
                <div class="category player-card">
                    <img src="images/<?php echo $player['image']; ?>" alt="<?php echo $player['name']; ?>">
                    <p><?php echo $player['name']; ?></p>
                    <button type="submit" name="player" value="<?php echo $player['name']; ?>">Select</button>
                </div>
            <?php endforeach; ?>
        </form>
    </div>
<?php elseif (empty($_SESSION['player_name'])): ?>
    <!-- New name input screen -->
    <div class="name-input-container">
        <h2>Enter Your Name</h2>
        <form method="POST">
            <input type="text" name="player_name" required placeholder="Enter your name" 
                   minlength="2" maxlength="20" pattern="[A-Za-z0-9 ]+"
                   title="Letters, numbers and spaces only (2-20 characters)">
            <button type="submit">Continue</button>
        </form>
    </div>
    <style>
        .name-input-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            padding: 20px;
            width: 90%;
            max-width: 400px;
            background-color: #00021d; /* Light background */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: middle;
            z-index: 1;
        }

        .name-input-container h2 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: white; /* Darker text */
        }

        .name-input-container form {
            width: 100%;
        }

        .name-input-container input {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        .name-input-container button {
            padding: 10px 15px;
            background-color: #90a4ae;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
            width: 100%;
        }

        .name-input-container button:hover {
            background-color: #0056b3; /* Darker blue */
        }
    </style>
<?php elseif ($_SESSION['category'] == ""): ?>
    <div class="category-container">
        <?php foreach ($categories as $category => $words): ?>
            <div class="category">
                <form method="POST">
                    <input type="hidden" name="category" value="<?php echo $category; ?>">
                    <img src="images/<?php echo strtolower($category); ?>.png" alt="<?php echo $category; ?>">
                    <p><?php echo $category; ?></p>
                    <button type="submit">Start Game</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif ($_SESSION['completed']): ?>
    <?php require_once 'dashboard.php'; ?>
    <div class="game-container">
        <a href="?reset=true" class="back-button"></a>
        <h2>Game Completed!</h2>
        <p>Player: <?php echo htmlspecialchars($_SESSION['player_name']); ?></p>
        <p>Category: <?php echo htmlspecialchars($_SESSION['category']); ?></p>
        <p>Final Score: <?php echo $_SESSION['score']; ?>/10</p>
    </div>
    <?php displayUserRankings($conn, $_SESSION['category']); ?>
<?php else: ?>
    <!-- Active game screen (keep existing code) -->
    <div class="game-container">
        <a href="?reset=true" class="back-button"></a>
        <p>Player: <strong><?php echo htmlspecialchars($_SESSION['player_name']); ?></strong></p>
        <p>Category: <strong><?php echo $_SESSION['category']; ?></strong></p>
        <div class="scrambled-word-container">
            <?php
                $letters = str_split($_SESSION['scrambledWord']);
                foreach ($letters as $index => $letter) {
                    echo "<div class='letter' style='animation-delay: " . ($index * 0.1) . "s;'>$letter</div>";
                }
            ?>
        </div>
        <p>Score: <?php echo $_SESSION['score']; ?></p>
        
        <form method="POST">
            <label for="guess">Your Guess:</label>
            <input type="text" name="guess" id="guess" required>
            <button type="submit">Submit Guess</button>
        </form>

        <p><?php echo $message; ?></p>
    </div>
<?php endif; ?>

</body>
</html>