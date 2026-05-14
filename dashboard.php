<?php
// dashboard.php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Protect this page
check_auth();

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_mood') {
    verify_csrf_token($_POST['csrf_token']);
    
    $quote = $_POST['quote'] ?? '';
    
    // Handle Video Upload
    $video_filename = '';
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "assets/media/";
        $video_filename = time() . '_' . basename($_FILES['video_file']['name']);
        $target_file = $target_dir . $video_filename;
        
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($fileType != "mp4" && $fileType != "mov" && $fileType != "webm") {
            $message = "Only MP4, MOV, and WEBM videos are allowed.";
        } else {
            if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
                // Video uploaded successfully
            } else {
                $message = "Error uploading video.";
            }
        }
    } else {
        $message = "Video file is required.";
    }

    // Handle Audio Upload (Optional)
    $audio_filename = '';
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "assets/media/";
        $audio_filename = time() . '_' . basename($_FILES['audio_file']['name']);
        $target_file = $target_dir . $audio_filename;
        
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($fileType != "mp3" && $fileType != "wav" && $fileType != "ogg") {
            $message = "Only MP3, WAV, and OGG audio are allowed.";
        } else {
            move_uploaded_file($_FILES['audio_file']['tmp_name'], $target_file);
        }
    }

    if (empty($message) && !empty($quote) && !empty($video_filename)) {
        // Fetch current media to delete after successful update
        $old_stmt = $pdo->query("SELECT video_filename, audio_filename FROM content ORDER BY created_at DESC LIMIT 1");
        $old_files = $old_stmt->fetch();

        $stmt = $pdo->prepare("INSERT INTO content (quote, video_filename, audio_filename) VALUES (?, ?, ?)");
        if ($stmt->execute([$quote, $video_filename, $audio_filename])) {
            // Cleanup old files
            if ($old_files) {
                $old_video = "assets/media/" . $old_files['video_filename'];
                if (file_exists($old_video)) unlink($old_video);
                
                if (!empty($old_files['audio_filename'])) {
                    $old_audio = "assets/media/" . $old_files['audio_filename'];
                    if (file_exists($old_audio)) unlink($old_audio);
                }
            }
            $message = "Mood updated successfully. Old media files have been cleared.";
        } else {
            $message = "Database update failed.";
        }
    }
}

// Fetch analytics
$today_stmt = $pdo->prepare("SELECT * FROM analytics WHERE date = ?");
$today_stmt->execute([date('Y-m-d')]);
$today_stats = $today_stmt->fetch() ?: ['views' => 0, 'hearts' => 0];

$last_7_stmt = $pdo->query("SELECT * FROM analytics ORDER BY date DESC LIMIT 7");
$trend = $last_7_stmt->fetchAll();

// Fetch current mood
$current_mood_stmt = $pdo->query("SELECT * FROM content ORDER BY created_at DESC LIMIT 1");
$current_mood = $current_mood_stmt->fetch();

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Araw-Araw Sakit</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav style="padding: 2rem; display: flex; justify-content: space-between; align-items: center; max-width: 1000px; margin: 0 auto;">
        <h2 style="font-weight: 600;">Admin Center</h2>
        <a href="logout.php" style="color: var(--accent); text-decoration: none; font-weight: 600;">Logout</a>
    </nav>

    <div class="dashboard-container">
        <!-- Analytics -->
        <div class="stats-grid">
            <div class="stat-card glass">
                <span class="stat-value"><?php echo number_format($today_stats['views']); ?></span>
                <span class="stat-label">Today's Views</span>
            </div>
            <div class="stat-card glass">
                <span class="stat-value"><?php echo number_format($today_stats['hearts']); ?></span>
                <span class="stat-label">Today's Hearts</span>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="error-msg" style="background: rgba(0, 255, 136, 0.1); color: #00ff88; margin-bottom: 2rem;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Content Manager -->
        <div class="admin-form glass">
            <h2>Update Daily Pain</h2>
            
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(255,255,255,0.02); border-radius: 12px; border-left: 4px solid var(--accent);">
                <small style="color: var(--text-secondary);">Currently Live:</small>
                <p style="margin-top: 0.5rem; font-style: italic;">"<?php echo $current_mood ? htmlspecialchars($current_mood['quote']) : 'None'; ?>"</p>
                <small style="display: block; margin-top: 0.5rem; color: var(--text-secondary);">Media: <?php echo $current_mood ? htmlspecialchars($current_mood['video_filename']) : 'None'; ?></small>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_mood">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary);">Filipino Quote</label>
                    <textarea name="quote" placeholder="Ano ang hugot mo ngayon?" required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary);">Upload Video (MP4/WEBM)</label>
                        <input type="file" name="video_file" accept="video/*" required>
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary);">Upload Audio (Optional - MP3)</label>
                        <input type="file" name="audio_file" accept="audio/*">
                    </div>
                </div>

                <button type="submit" class="btn-primary">I-update ang Mood</button>
            </form>
        </div>

        <!-- 7-Day Trend -->
        <div class="glass" style="margin-top: 3rem; padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">7-Day Trend</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <th style="padding: 1rem; color: var(--text-secondary);">Date</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">Views</th>
                        <th style="padding: 1rem; color: var(--text-secondary);">Hearts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trend as $row): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                            <td style="padding: 1rem;"><?php echo $row['date']; ?></td>
                            <td style="padding: 1rem;"><?php echo number_format($row['views']); ?></td>
                            <td style="padding: 1rem;"><?php echo number_format($row['hearts']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
