<?php
// data.php - Fetches summary data and provides graph data

include_once 'db_connect.php';

// User info
$user_info = [
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'position' => 'System Operator'
];

// Function to get a single count value from the database
function get_count($conn, $query) {
    $result = $conn->query($query);
    return $result->fetch_row()[0] ?? 0;
}

// **CORRECTED**: Merged dynamic values with static graph data
$dashboard_metrics = [
    'total_users' => [
        'label' => 'Total Users', 'value' => get_count($conn, "SELECT COUNT(*) FROM users"),
        'icon' => 'fas fa-users',
        'graph' => [ 'type' => 'line', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'dataPoints' => [12, 15, 20, 18, 25, 23, 30] ]
    ],
    'unread_feedback' => [
        'label' => 'Unread Feedback', 'value' => get_count($conn, "SELECT COUNT(*) FROM feedback WHERE is_read = 0"),
        'icon' => 'fas fa-comment-dots',
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [12, 19, 3, 5, 22] ]
    ],
    'funds' => [
        'label' => 'Funds', 'value' => '$' . number_format(get_count($conn, "SELECT SUM(amount) FROM funds WHERE type = 'Deposit' OR type = 'Revenue'")),
        'icon' => 'fas fa-dollar-sign',
        'graph' => [ 'type' => 'bar', 'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'], 'dataPoints' => [45000, 52000, 38000, 50900] ]
    ],
    'work_completions' => [
        'label' => 'Work Completions', 'value' => round(get_count($conn, "SELECT AVG(completion_percentage) FROM work_completions")) . '%',
        'icon' => 'fas fa-check-circle',
        'graph' => [ 'type' => 'line', 'labels' => ['Project A', 'Project B', 'Project C', 'Project D'], 'dataPoints' => [95, 80, 75, 90], 'tension' => 0.4 ]
    ],
    'comments' => [
        'label' => 'Comments', 'value' => get_count($conn, "SELECT COUNT(*) FROM comments"),
        'icon' => 'fas fa-comments',
        'graph' => [ 'type' => 'bar', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [45, 60, 51, 78, 70] ]
    ],
    'total_tasks' => [
        'label' => 'Total Tasks', 'value' => get_count($conn, "SELECT COUNT(*) FROM tasks WHERE status != 'Completed'"),
        'icon' => 'fas fa-tasks',
        'graph' => [ 'type' => 'line', 'labels' => ['Pending', 'In Progress', 'Overdue'], 'dataPoints' => [40, 35, 5], 'fill' => true ]
    ],
    'emails' => [
        'label' => 'New Emails', 'value' => get_count($conn, "SELECT COUNT(*) FROM emails"),
        'icon' => 'fas fa-envelope',
        'graph' => [ 'type' => 'bar', 'labels' => ['Inbox', 'Spam', 'Drafts'], 'dataPoints' => [458, 90, 15] ]
    ],
    'requests' => [
        'label' => 'Pending Requests', 'value' => get_count($conn, "SELECT COUNT(*) FROM requests WHERE status = 'Pending'"),
        'icon' => 'fas fa-concierge-bell',
        'graph' => [ 'type' => 'line', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'], 'dataPoints' => [10, 15, 8, 12, 2] ]
    ],
    'offers' => [
        'label' => 'Active Offers', 'value' => get_count($conn, "SELECT COUNT(*) FROM offers WHERE expiry_date >= CURDATE()"),
        'icon' => 'fas fa-tags',
        'graph' => [ 'type' => 'bar', 'labels' => ['Discount', 'Promo', 'Bundle'], 'dataPoints' => [15, 8, 7] ]
    ]
];
?>