<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <?php
        // Establishes database connection
        include 'db_connect.php';

        // Gets the metric from the URL (e.g., 'total_users')
        $metric_key = $_GET['metric'] ?? '';

        // Prepare variables
        $page_title = 'Details';
        $headers = [];
        $rows = [];

        // This switch statement fetches the correct data for the selected metric
        switch ($metric_key) {
            case 'total_users':
                $page_title = 'All Users';
                $headers = ['ID', 'Name', 'Email', 'Join Date', 'Status'];
                $result = $conn->query("SELECT id, name, email, join_date, status FROM users ORDER BY id");
                break;
            case 'unread_feedback':
                $page_title = 'Unread Feedback';
                $headers = ['ID', 'User', 'Message', 'Received Date'];
                $result = $conn->query("SELECT id, user_name, message, received_date FROM feedback WHERE is_read = 0 ORDER BY received_date DESC");
                break;
            case 'funds':
                $page_title = 'Financial Transactions';
                $headers = ['ID', 'Amount', 'Type', 'Transaction Date', 'Status'];
                $result = $conn->query("SELECT id, amount, type, transaction_date, status FROM funds ORDER BY transaction_date DESC");
                break;
            case 'work_completions':
                $page_title = 'Work Completions';
                $headers = ['ID', 'Project Name', 'Team Lead', 'Completion %', 'Deadline'];
                $result = $conn->query("SELECT id, project_name, team_lead, completion_percentage, deadline FROM work_completions ORDER BY deadline");
                break;
            case 'comments':
                $page_title = 'All Comments';
                $headers = ['ID', 'User Name', 'Post Title', 'Comment', 'Date'];
                $result = $conn->query("SELECT id, user_name, post_title, comment_text, comment_date FROM comments ORDER BY comment_date DESC");
                break;
            case 'total_tasks':
                $page_title = 'All Tasks';
                $headers = ['ID', 'Assigned To', 'Task', 'Due Date', 'Status'];
                $result = $conn->query("SELECT id, assigned_to, task_description, due_date, status FROM tasks ORDER BY due_date");
                break;
            case 'emails':
                $page_title = 'New Emails';
                $headers = ['ID', 'From', 'Subject', 'Received', 'Priority'];
                $result = $conn->query("SELECT id, sender, subject, received_date, priority FROM emails ORDER BY received_date DESC");
                break;
            case 'requests':
                $page_title = 'All Requests';
                $headers = ['ID', 'Type', 'Submitted By', 'Date', 'Status'];
                $result = $conn->query("SELECT id, request_type, submitted_by, request_date, status FROM requests ORDER BY request_date DESC");
                break;
            case 'offers':
                $page_title = 'All Offers';
                $headers = ['ID', 'Code', 'Type', 'Discount', 'Expires'];
                $result = $conn->query("SELECT id, offer_code, offer_type, discount_value, expiry_date FROM offers ORDER BY expiry_date");
                break;
            default:
                // Fallback if the metric is unknown
                $page_title = 'Error';
                $headers = ['Status'];
                $rows = [['Data not found for this metric.']];
                $result = null;
        }

        // Populates the $rows array with data from the database
        if ($result) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
        }
    ?>
    <div class="dashboard-container">
        <?php include 'partials/sidebar.php'; ?>

        <main class="main-content">
            <?php include 'partials/header.php'; ?>

            <div class="details-container">
                <div class="details-header">
                    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                    <button id="add-new-btn" class="action-btn primary" data-metric="<?php echo $metric_key; ?>"><i class="fas fa-plus"></i> Add New Entry</button>
                </div>
                
                <div class="details-table-wrapper">
                    <table class="details-table" id="details-table">
                        <thead>
                            <tr>
                                <?php foreach ($headers as $header): ?>
                                    <th><?php echo htmlspecialchars($header); ?></th>
                                <?php endforeach; ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr data-id="<?php echo htmlspecialchars($row['id']); ?>">
                                        <?php foreach ($row as $cell): ?>
                                            <td><?php echo htmlspecialchars($cell); ?></td>
                                        <?php endforeach; ?>
                                        <td class="actions-cell">
                                            <button class="action-icon-btn edit-btn" title="Edit"><i class="fas fa-pencil-alt"></i></button>
                                            <button class="action-icon-btn delete-btn" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo count($headers) + 1; ?>" style="text-align: center;">No data available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="modal-overlay" class="modal-overlay hidden">
        <div id="data-modal" class="modal">
            <button id="close-modal-btn" class="modal-close-btn">&times;</button>
            <h2 id="modal-title">Modal Title</h2>
            <form id="data-form">
                <div id="modal-form-content">
                    </div>
                <div class="form-actions">
                    <button type="submit" class="action-btn primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>