CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fio VARCHAR(150) NOT NULL,
    login VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user'
);
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    start_date DATE NOT NULL,
    payment_method VARCHAR(80) NOT NULL,
    status ENUM('Новая','Идет обучение','Обучение завершено') NOT NULL DEFAULT 'Новая',
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);
