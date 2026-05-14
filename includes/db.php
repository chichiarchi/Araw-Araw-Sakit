<?php
// includes/db.php

$db_file = __DIR__ . '/../database.sqlite';

try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize schema
    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        quote TEXT NOT NULL,
        video_filename VARCHAR(255) NOT NULL,
        audio_filename VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS analytics (
        date DATE PRIMARY KEY,
        views INTEGER DEFAULT 0,
        hearts INTEGER DEFAULT 0
    )");

    // Ensure today's analytics row exists
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO analytics (date, views, hearts) VALUES (?, 0, 0)");
    $stmt->execute([$today]);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
