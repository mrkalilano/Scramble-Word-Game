<?php
function displayUserRankings($conn, $category = null) {
    $where_clause = $category ? "WHERE category = ?" : "";
    $query = "
        SELECT 
            player_name,
            avatar,
            score,
            category,
            played_at
        FROM scores
        $where_clause
        ORDER BY score DESC, played_at DESC
        LIMIT 10
    ";
    
    $stmt = $conn->prepare($query);
    if ($category) {
        $stmt->bind_param("s", $category);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<div class="dashboard-container">';
    echo '<h2>DASHBOARD</h2>';
    echo '<table class="rankings-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Category</th>
                    <th>Score</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';
    
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $rank++ . '</td>
                <td>
                    <div class="player-info">
                        <img src="images/default.png' . htmlspecialchars($row['avatar']) . '" class="avatar" alt="Player Avatar">
                        <span>' . htmlspecialchars($row['player_name']) . '</span>
                    </div>
                </td>
                <td>' . htmlspecialchars($row['category']) . '</td>
                <td>' . $row['score'] . '/10</td>
                <td>' . date('Y-m-d H:i', strtotime($row['played_at'])) . '</td>
              </tr>';
    }
    
    echo '</tbody></table></div>';
    
    $stmt->close();
}
echo '<style>
.dashboard-container {
    background: #90a4ae;
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.dashboard-title {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 1.5rem;
    text-align: center;
    font-weight: 600;
}

.table-container {
    overflow-x: auto;
}

.rankings-table {
    width: 100%;
    border-spacing: 0;
    font-size: 0.95rem;
}

.rankings-table th {
    color: white;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.8rem;
    padding: 1rem;
    background: #00021d;
    letter-spacing: 0.05em;
    border-radius: 25px;
}

.rankings-table td {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.player-info {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.player-name {
    font-weight: 500;
    color: #333;
}

.rank {
    font-weight: 600;
    color: #666;
}

.score {
    font-weight: 500;
    color: #2196f3;
}

.date {
    color: #888;
    font-size: 0.9rem;
}

/* Top 3 Ranks */
.rank-1 {
    background: linear-gradient(45deg, rgba(255,215,0,0.05) 0%, rgba(255,215,0,0) 100%);
}

.rank-2 {
    background: linear-gradient(45deg, rgba(192,192,192,0.05) 0%, rgba(192,192,192,0) 100%);
}

.rank-3 {
    background: linear-gradient(45deg, rgba(205,127,50,0.05) 0%, rgba(205,127,50,0) 100%);
}

@media (max-width: 768px) {
    .dashboard-container {
        margin: 1rem;
        padding: 1rem;
    }
    
    .rankings-table td, 
    .rankings-table th {
        padding: 0.8rem 0.5rem;
    }
    
    .date {
        display: none;
    }
}
</style>
';
?>