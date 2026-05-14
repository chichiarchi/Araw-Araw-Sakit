<?php
// index.php
require_once 'includes/db.php';

// Increment views for today
$today = date('Y-m-d');
$pdo->prepare("UPDATE analytics SET views = views + 1 WHERE date = ?")->execute([$today]);

// Fetch latest content
$stmt = $pdo->query("SELECT * FROM content ORDER BY created_at DESC LIMIT 1");
$content = $stmt->fetch();

// Default content if none exists
if (!$content) {
    $content = [
        'quote' => 'Handa ka na bang masaktan muli?',
        'video_filename' => 'default.mp4',
        'audio_filename' => null
    ];
}
?>
<!DOCTYPE html>
<html lang="tl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araw-Araw Sakit | Ang Iyong Kasama sa Pagluha</title>
    <meta name="description" content="Isang sining para sa mga pusong sugatan.">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div id="landing" class="landing-overlay">
        <button id="start-btn" class="btn-primary" style="width: auto; padding: 1.5rem 3rem; font-size: 1.2rem;">
            Handa ka na bang masaktan?
        </button>
    </div>

    <main class="main-experience" style="display: none;">
        <video id="bg-video" class="bg-video" loop playsinline>
            <source src="assets/media/<?php echo htmlspecialchars($content['video_filename']); ?>" type="video/mp4">
        </video>
        
        <?php if ($content['audio_filename']): ?>
        <audio id="bg-audio" loop>
            <source src="assets/media/<?php echo htmlspecialchars($content['audio_filename']); ?>" type="audio/mpeg">
        </audio>
        <?php endif; ?>

        <div class="quote-container">
            <h2 class="quote-text"><?php echo nl2br(htmlspecialchars($content['quote'])); ?></h2>
        </div>

        <button id="heart-btn" class="heart-button">
            ❤️
        </button>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
