<?php 
    // This file now requires data.php to be included before it
    if (!isset($dashboard_metrics)) {
        include_once __DIR__ . '/../data.php';
    }
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Dashboard</h2>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li><a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            
            <?php foreach ($dashboard_metrics as $key => $metric): ?>
                <li>
                    <a href="details.php?metric=<?php echo $key; ?>" class="nav-link">
                        <i class="<?php echo $metric['icon']; ?>"></i> <?php echo $metric['label']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div class="sidebar-footer">
       <a href="#" id="logout-btn" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</aside>