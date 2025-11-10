<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_id = get_user_id();
$user_projects = get_user_projects($user_id);
$notifications = get_user_notifications($user_id, 5);
$unread_count = get_unread_notification_count($user_id);

// Get recent tasks
global $pdo;
$stmt = $pdo->prepare("
    SELECT t.*, p.name as project_name, p.color as project_color, 
           ts.name as status_name, ts.color as status_color
    FROM tasks t
    JOIN projects p ON t.project_id = p.id
    JOIN task_statuses ts ON t.status_id = ts.id
    WHERE t.assigned_to = ? OR t.created_by = ?
    ORDER BY t.created_at DESC
    LIMIT 10
");
$stmt->execute([$user_id, $user_id]);
$recent_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <div class="dashboard">
            <div class="dashboard-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo $_SESSION['user_name']; ?>!</p>
            </div>

            <div class="dashboard-grid">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #3498db;">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo count($user_projects); ?></h3>
                            <p>Active Projects</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #e74c3c;">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php 
                                $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE assigned_to = ?");
                                $stmt->execute([$user_id]);
                                echo $stmt->fetch()['count'];
                            ?></h3>
                            <p>Assigned Tasks</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #f39c12;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $unread_count; ?></h3>
                            <p>Unread Notifications</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Projects -->
                <div class="dashboard-section">
                    <h2>Recent Projects</h2>
                    <div class="projects-grid compact">
                        <?php foreach (array_slice($user_projects, 0, 6) as $project): ?>
                            <div class="project-card" style="border-left-color: <?php echo $project['color']; ?>">
                                <h3><?php echo $project['name']; ?></h3>
                                <p><?php echo $project['description']; ?></p>
                                <div class="project-meta">
                                    <span class="role-badge"><?php echo $project['member_role']; ?></span>
                                    <a href="project_detail.php?id=<?php echo $project['id']; ?>" class="btn btn-sm">
                                        View Project
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($user_projects)): ?>
                        <div class="empty-state">
                            <i class="fas fa-project-diagram fa-3x"></i>
                            <h3>No Projects Yet</h3>
                            <p>Get started by creating your first project</p>
                            <a href="projects.php" class="btn btn-primary">Create Project</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Tasks -->
                <div class="dashboard-section">
                    <h2>Recent Tasks</h2>
                    <div class="tasks-list">
                        <?php foreach ($recent_tasks as $task): ?>
                            <div class="task-item">
                                <div class="task-main">
                                    <h4><?php echo $task['title']; ?></h4>
                                    <div class="task-meta">
                                        <span class="project-tag" style="background: <?php echo $task['project_color']; ?>">
                                            <?php echo $task['project_name']; ?>
                                        </span>
                                        <span class="status-tag" style="background: <?php echo $task['status_color']; ?>">
                                            <?php echo $task['status_name']; ?>
                                        </span>
                                        <?php if ($task['due_date']): ?>
                                            <span class="due-date <?php echo (strtotime($task['due_date']) < time()) ? 'overdue' : ''; ?>">
                                                <i class="fas fa-calendar"></i> <?php echo date('M j', strtotime($task['due_date'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a href="task_detail.php?id=<?php echo $task['id']; ?>" class="btn btn-sm">
                                    View Task
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($recent_tasks)): ?>
                            <div class="empty-state">
                                <i class="fas fa-tasks fa-3x"></i>
                                <h3>No Tasks Yet</h3>
                                <p>Tasks assigned to you will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="dashboard-section">
                    <h2>Recent Notifications</h2>
                    <div class="notifications-list">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                                <div class="notification-icon">
                                    <i class="fas fa-<?php 
                                        switch($notification['type']) {
                                            case 'task_assigned': echo 'tasks'; break;
                                            case 'comment': echo 'comment'; break;
                                            case 'status_change': echo 'exchange-alt'; break;
                                            case 'project_invite': echo 'user-plus'; break;
                                            default: echo 'bell';
                                        }
                                    ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <h4><?php echo $notification['title']; ?></h4>
                                    <p><?php echo $notification['message']; ?></p>
                                    <small><?php echo date('M j, g:i A', strtotime($notification['created_at'])); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($notifications)): ?>
                            <div class="empty-state">
                                <i class="fas fa-bell fa-3x"></i>
                                <h3>No Notifications</h3>
                                <p>You're all caught up!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php require_once 'includes/footer.php'; ?>