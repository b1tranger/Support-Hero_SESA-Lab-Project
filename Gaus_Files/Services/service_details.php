<!-- when clicking "view" from index.php->service section, it shows that service's details -->

<?php
session_start();
include("../connection.php");

// 1. Get User Session Info
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$user_type = $_SESSION['user_type'] ?? 'visitor'; // Use null coalescing operator for safety
$username = $_SESSION['username'] ?? 'Visitor';

// 2. Get the Service ID from the URL
$service_to_show = null;
$error_message = '';

if (isset($_GET['id'])) {
    $service_id = (int) $_GET['id']; // Cast to integer for security

    // 3. Fetch the single service using a prepared statement
    $sql = "SELECT * FROM service WHERE service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $service_to_show = $result->fetch_assoc();
    } else {
        $error_message = "Service not found. It may have been deleted or the ID is incorrect.";
    }
    $stmt->close();
} else {
    $error_message = "No service ID was provided.";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title is dynamic! -->
    <title><?php echo $service_to_show ? htmlspecialchars($service_to_show['service_name']) : 'Service Not Found'; ?>
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="service.css">
</head>

<body>

    <div class="list-container">
        <header class="list-header">
            <!-- Header is dynamic! -->
            <h1><?php echo $service_to_show ? 'Service Details' : 'Error'; ?></h1>
            <div>
                <a href="service.php" class="btn btn-back">Back to Full List</a>
                <a href="../Home_Page/index.php" class="btn btn-back">Back to Home</a>
                <?php if ($user_type == 'admin'): ?>
                    <a href="../Home_Page/admin.php" class="btn btn-back">Back to Dashboard</a>
                <?php endif ?>
            </div>
        </header>

        <!-- Use "service-grid" class so the JS from service.php works without changes -->
        <!-- Added "detail-page" class for specific styling -->
        <main class="service-grid detail-page">
            <?php
            // 4. Check if we found a service
            if ($service_to_show):
                // Get data for the service
                $service_id = $service_to_show['service_id'];
                $service_name = htmlspecialchars($service_to_show['service_name']);

                // Show the FULL details, not a snippet
                $service_desc = htmlspecialchars($service_to_show['details']);

                $service_type = htmlspecialchars($service_to_show['service_type']);
                $service_poster = htmlspecialchars($service_to_show['username']);
                $deadline = htmlspecialchars(date("l, d F, Y", strtotime($service_to_show['deadline']))); // More detailed date
                $compensation = htmlspecialchars($service_to_show['compensation']);
                $status = htmlspecialchars($service_to_show['status']);
                $accept_count = (int) $service_to_show['accept_count'];

                // --- Re-using Button Visibility Logic from service.php ---
                $can_accept = false;
                if ($user_type == 'provider' || $user_type == 'admin') {
                    $can_accept = true;
                }

                $can_complete = false;
                if ($user_type == 'consumer' || $user_type == 'admin') {
                    $can_complete = true;
                }
                // --- End of button logic ---
            
                ?>

                <!-- 5. Display the Single Service Card -->
                <div class="service-card" id="service-<?php echo $service_id; ?>">
                    <div class="card-header">
                        <h3><?php echo $service_name; ?></h3>
                        <span class="status-indicator status-<?php echo $status; ?>"
                            title="Status: <?php echo ucfirst($status); ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Using service-meta for all key details -->
                        <p class="service-meta">
                            <strong>Posted by:</strong> <?php echo $service_poster; ?>
                        </p>
                        <p class="service-meta">
                            <strong>Service Type:</strong> <?php echo ucfirst($service_type); ?>
                        </p>
                        <p class="service-meta">
                            <strong>Compensation:</strong> <?php echo $compensation; ?>
                        </p>
                        <p class="service-meta">
                            <strong>Deadline:</strong> <?php echo $deadline; ?>
                        </p>

                        <!-- Section for the full details -->
                        <h4 class="detail-heading">Service Details:</h4>
                        <p class="service-details full-details">
                            <?php echo nl2br($service_desc); // nl2br respects line breaks ?></p>
                    </div>
                    <div class="card-actions">
                        <?php if ($can_accept): ?>
                            <button class="btn btn-accept" data-service-id="<?php echo $service_id; ?>">
                                Accept (<span class="accept-count"><?php echo $accept_count; ?></span>)
                            </button>
                        <?php endif; ?>

                        <?php if ($can_complete): ?>
                            <button class="btn btn-complete" data-service-id="<?php echo $service_id; ?>">
                                Mark as Completed
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                // 6. Else, show the error message
            else:
                echo '<p class="no-services">' . $error_message . '</p>';
            endif;
            ?>
        </main>
    </div>

    <!-- 
        7. Copied the ENTIRE script from service.php
        It will work because the <main> tag has the "service-grid" class
        and the card has the same structure.
    -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // We select the main container. Since it has the "service-grid" class,
            // this JavaScript works exactly the same as on service.php
            const grid = document.querySelector('.service-grid');

            grid.addEventListener('click', async (e) => {
                // --- Handle "Accept" Button Click ---
                if (e.target.classList.contains('btn-accept')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;

                    const formData = new FormData();
                    formData.append('action', 'accept');
                    formData.append('service_id', serviceId);

                    try {
                        const response = await fetch('update_service.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.success) {
                            const card = document.getElementById(`service-${serviceId}`);
                            const countSpan = button.querySelector('.accept-count');
                            const statusSpan = card.querySelector('.status-indicator');

                            countSpan.textContent = result.new_count;
                            statusSpan.textContent = 'In Progress';
                            statusSpan.classList.remove('status-pending');
                            statusSpan.classList.add('status-in_progress');
                            button.style.display = 'none';

                            <?php if ($user_type == 'provider' || $user_type == 'admin'): ?>
                                const completeBtn = card.querySelector('.btn-complete');
                                if (!completeBtn) {
                                    const newCompleteBtn = document.createElement('button');
                                    newCompleteBtn.className = 'btn btn-complete';
                                    newCompleteBtn.dataset.serviceId = serviceId;
                                    newCompleteBtn.textContent = 'Mark as Completed';
                                    card.querySelector('.card-actions').appendChild(newCompleteBtn);
                                }
                            <?php endif; ?>

                        } else {
                            console.error('Failed to accept:', result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }

                // --- Handle "Completed" Button Click ---
                if (e.target.classList.contains('btn-complete')) {
                    const button = e.target;
                    const serviceId = button.dataset.serviceId;

                    const formData = new FormData();
                    formData.append('action', 'complete');
                    formData.append('service_id', serviceId);

                    try {
                        const response = await fetch('update_service.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.success) {
                            const card = document.getElementById(`service-${serviceId}`);

                            <?php if ($user_type == 'admin'): ?>
                                // Admin sees status change
                                const statusSpan = card.querySelector('.status-indicator');
                                statusSpan.textContent = 'Completed';
                                statusSpan.classList.remove('status-in_progress');
                                statusSpan.classList.add('status-completed');
                                card.querySelector('.card-actions').innerHTML = ''; // Clear buttons
                            <?php else: ?>
                                // Regular users see it fade and go
                                card.style.opacity = '0';
                                // Optional: Redirect back to the list after it's gone
                                setTimeout(() => {
                                    card.remove();
                                    // window.location.href = 'service.php'; // Uncomment to redirect
                                }, 300);
                            <?php endif; ?>

                        } else {
                            console.error('Failed to complete:', result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                }
            });
        });
    </script>
</body>

</html>