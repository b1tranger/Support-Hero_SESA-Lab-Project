<?php
// api.php - Handles all database operations for ALL tables

include 'db_connect.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];
$action = $_POST['action'] ?? '';
$metric = $_POST['metric'] ?? '';
$id = $_POST['id'] ?? 0;

// --- Security Whitelist: Maps the frontend metric to the real database table name ---
// This is a crucial security step to prevent SQL injection.
$table_map = [
    'total_users' => 'users',
    'unread_feedback' => 'feedback',
    'funds' => 'funds',
    'work_completions' => 'work_completions',
    'comments' => 'comments',
    'total_tasks' => 'tasks',
    'emails' => 'emails',
    'requests' => 'requests',
    'offers' => 'offers'
];

// Get the actual table name from our secure map
$table_name = $table_map[$metric] ?? null;

// --- Main Action Handler ---

if ($action === 'deleteEntry') {
    if ($table_name && $id > 0) {
        // We can safely use $table_name because it comes from our whitelist map, not user input.
        $sql = "DELETE FROM `$table_name` WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Entry deleted successfully.'];
            } else {
                $response['message'] = 'Database execute failed: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Database prepare failed: ' . $conn->error;
        }
    } else {
        $response['message'] = 'Invalid table or ID provided for deletion.';
    }
}

// --- Placeholder for ADD and UPDATE logic ---
// You would build out these sections using the same $table_name logic
if ($action === 'addEntry' || $action === 'updateEntry') {
    if (!$table_name) {
        $response['message'] = 'Invalid metric specified.';
    } else {
        // This is a simplified example. A real implementation would be more robust.
        $response = ['status' => 'success', 'message' => 'Action ' . $action . ' completed.'];
    }
}


echo json_encode($response);
$conn->close();
?>