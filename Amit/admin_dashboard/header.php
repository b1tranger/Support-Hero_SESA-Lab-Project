<header class="main-header">
    <h1><?php echo $page_title ?? 'Admin Dashboard'; ?></h1>
    <div class="user-profile">
        <div class="user-info">
            <span class="user-name"><?php echo $user_info['name']; ?></span>
            <span class="user-email"><?php echo $user_info['email']; ?></span>
        </div>
    </div>
</header>