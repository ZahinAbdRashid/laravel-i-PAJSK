-- users table (untuk semua role)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ic_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- teachers table (additional info untuk teacher)
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    staff_id VARCHAR(50) UNIQUE,
    subject VARCHAR(100),
    assigned_class ENUM('Alpha', 'Delta', 'Omega') NOT NULL,
    phone VARCHAR(20),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- students table (additional info untuk student)
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    class ENUM('Alpha', 'Delta', 'Omega') NOT NULL,
    academic_session VARCHAR(20),
    semester INT,
    teacher_id INT, 
    sports VARCHAR(100),
    club VARCHAR(100),
    uniform VARCHAR(100),
    position VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);