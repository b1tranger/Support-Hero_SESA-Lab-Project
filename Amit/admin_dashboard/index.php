<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'data.php'; ?>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <?php $page_title = 'Dashboard Overview'; include 'header.php'; ?>
            
            <section class="dashboard-cards">
                <?php foreach ($dashboard_metrics as $key => $metric): ?>
                    <div class="card clickable" data-metric="<?php echo $key; ?>">
                        <div class="card-icon"><i class="<?php echo $metric['icon']; ?>"></i></div>
                        <div class="card-info">
                            <p class="value"><?php echo $metric['value']; ?></p>
                            <h3><?php echo $metric['label']; ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>
    </div>

    <div id="graph-modal-overlay" class="modal-overlay hidden">
        <div id="graph-modal" class="modal">
            <button id="close-graph-modal-btn" class="modal-close-btn">&times;</button>
            <h2 id="graph-modal-title">Metric Summary</h2>
            <div class="chart-container"><canvas id="summary-chart"></canvas></div>
            <div class="modal-actions">
                <a href="#" id="view-details-btn" class="action-btn primary">View Full Details</a>
            </div>
        </div>
    </div>
    
    <script id="dashboard-data" type="application/json">
        <?php echo json_encode($dashboard_metrics); ?>
    </script>
    
    <script src="script.js"></script>
</body>

</html>
