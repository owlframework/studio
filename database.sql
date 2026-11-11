CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    login_attempts VARCHAR(20) DEFAULT NULL,
    lockout_time DATETIME NULL,
    status ENUM('active','inactive','suspended','banned') DEFAULT 'inactive',
    role ENUM('user','admin','super_admin') DEFAULT 'user',
    code VARCHAR(6) DEFAULT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_code (code),
    INDEX idx_username (username),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (
    username, 
    email, 
    phone, 
    password, 
    email_verified_at, 
    phone_verified_at, 
    status, 
    role, 
    code
) VALUES (
    'admin', 
    'admin@example.com', 
    '+1234567890', 
    '$2y$10$i5Qv0M4iZ0U07wFp26M7gexX8VnBwzY3vX6p5z.H88qB2w9BfKeii', -- Hashed version of '12345678'
    CURRENT_TIMESTAMP, 
    CURRENT_TIMESTAMP, 
    'active', 
    'admin', 
    NULL
);


CREATE TABLE mailer_settings (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  host VARCHAR(255) NOT NULL DEFAULT '',
  username VARCHAR(255) NOT NULL DEFAULT '',
  password VARCHAR(255) NOT NULL DEFAULT '',
  port INT(11) NOT NULL DEFAULT 465,
  from_name VARCHAR(255) NOT NULL DEFAULT '',
  from_email VARCHAR(255) NOT NULL DEFAULT '',
  replyto VARCHAR(255) NOT NULL DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Safe default insert so it doesn't fail NOT NULL constraints
INSERT INTO mailer_settings (host, username, password, port, from_name, from_email, replyto) 
VALUES ('smtp.example.com', 'user@example.com', 'password', 465, 'Owl Framework', 'no-reply@example.com', 'support@example.com');


CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used TINYINT(1) DEFAULT 0, 
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT NULL,
    last_activity INT,
    ip_address VARCHAR(45),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;