use medicabinet; 
CREATE TABLE if not exists privilege  (
    id INT AUTO_INCREMENT PRIMARY KEY,
    privilege VARCHAR(50) NOT NULL
);

INSERT INTO privilege (id, privilege) VALUES
(1, 'Admin'),
(2, 'Client');

CREATE TABLE if not exists users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    privilege_id INT NOT NULL,
    patient_id INT UNSIGNED NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user_privilege FOREIGN KEY (privilege_id) REFERENCES privilege(id),
    CONSTRAINT fk_user_patient FOREIGN KEY (patient_id) REFERENCES patients(id)
);