-- Project Management Tool Database
CREATE DATABASE IF NOT EXISTS project_management;
USE project_management;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT 'default.png',
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    status ENUM('active', 'completed', 'archived') DEFAULT 'active',
    color VARCHAR(7) DEFAULT '#3498db',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Project members (many-to-many relationship)
CREATE TABLE project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'admin', 'member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_member (project_id, user_id)
);

-- Task statuses
CREATE TABLE task_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    color VARCHAR(7) DEFAULT '#95a5a6',
    project_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Tasks table
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    project_id INT NOT NULL,
    status_id INT NOT NULL,
    assigned_to INT,
    created_by INT NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    due_date DATE,
    estimated_hours DECIMAL(5,2),
    actual_hours DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (status_id) REFERENCES task_statuses(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- Attachments table
CREATE TABLE attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    task_id INT NOT NULL,
    uploaded_by INT NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('task_assigned', 'comment', 'status_change', 'project_invite') DEFAULT 'task_assigned',
    related_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default task statuses for sample projects
INSERT INTO task_statuses (name, color, project_id, sort_order) VALUES
('To Do', '#e74c3c', 1, 1),
('In Progress', '#f39c12', 1, 2),
('Review', '#3498db', 1, 3),
('Done', '#27ae60', 1, 4),
('Backlog', '#95a5a6', 2, 1),
('To Do', '#e74c3c', 2, 2),
('In Progress', '#f39c12', 2, 3),
('Testing', '#9b59b6', 2, 4),
('Done', '#27ae60', 2, 5);

-- Insert sample data
INSERT INTO users (name, email, password) VALUES
('Admin User', 'admin@project.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password
('John Doe', 'john@project.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane Smith', 'jane@project.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO projects (name, description, created_by, color) VALUES
('Website Redesign', 'Complete redesign of company website', 1, '#e74c3c'),
('Mobile App Development', 'New mobile application development', 1, '#3498db'),
('Marketing Campaign', 'Q4 marketing campaign planning', 2, '#2ecc71');

INSERT INTO project_members (project_id, user_id, role) VALUES
(1, 1, 'owner'), (1, 2, 'member'), (1, 3, 'member'),
(2, 1, 'owner'), (2, 2, 'admin'),
(3, 2, 'owner'), (3, 1, 'member');

INSERT INTO tasks (title, description, project_id, status_id, assigned_to, created_by, priority, due_date) VALUES
('Design Homepage', 'Create new homepage design mockups', 1, 1, 2, 1, 'high', '2024-02-15'),
('Develop Header', 'Implement responsive header component', 1, 2, 3, 1, 'medium', '2024-02-20'),
('API Integration', 'Integrate with backend APIs', 2, 3, 2, 1, 'high', '2024-02-25'),
('User Testing', 'Conduct user testing sessions', 1, 4, 3, 2, 'medium', '2024-03-01');