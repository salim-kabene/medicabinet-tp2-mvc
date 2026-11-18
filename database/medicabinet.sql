-- MédiCabinet TP3 | MySQL 8.0.16 ou supérieur
-- Réinitialisation destructive des huit tables du TP3.
-- Créer cette base dans le panneau de l’hébergeur avant l’import.
-- Sauvegarder toute donnée à conserver. Chaque import efface aussi les comptes et les visites.
-- Pour un test local, remplacer uniquement le nom de la base ci-dessous.

USE `medicabinet`;
SET NAMES utf8mb4;

-- Supprimer les tables enfants avant leurs parents.
DROP TABLE IF EXISTS logs;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS appointment_service;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS privilege;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS patients;

CREATE TABLE IF NOT EXISTS patients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    birth_date DATE NOT NULL,
    phone VARCHAR(25) NOT NULL,
    email VARCHAR(254) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS doctors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    specialty VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL,
    CONSTRAINT uq_services_name UNIQUE (name),
    CONSTRAINT chk_services_duration CHECK (duration_minutes > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id INT UNSIGNED NOT NULL,
    doctor_id INT UNSIGNED NOT NULL,
    starts_at DATETIME NOT NULL,
    status ENUM('planned', 'confirmed', 'completed', 'cancelled', 'absent')
        NOT NULL DEFAULT 'planned',
    reason VARCHAR(500) NULL,
    INDEX idx_appointments_starts_at (starts_at),
    INDEX idx_appointments_patient (patient_id),
    INDEX idx_appointments_doctor (doctor_id),
    CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id)
        REFERENCES patients(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
    CONSTRAINT fk_appointments_doctor FOREIGN KEY (doctor_id)
        REFERENCES doctors(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS appointment_service (
    appointment_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (appointment_id, service_id),
    INDEX idx_appointment_service_service (service_id),
    CONSTRAINT fk_appointment_service_appointment FOREIGN KEY (appointment_id)
        REFERENCES appointments(id) ON DELETE CASCADE ON UPDATE RESTRICT,
    CONSTRAINT fk_appointment_service_service FOREIGN KEY (service_id)
        REFERENCES services(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS privilege (
    id INT AUTO_INCREMENT PRIMARY KEY,
    privilege VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    photo VARCHAR(255) NULL,
    privilege_id INT NOT NULL,
    patient_id INT UNSIGNED NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user_privilege FOREIGN KEY (privilege_id) REFERENCES privilege(id),
    CONSTRAINT fk_user_patient FOREIGN KEY (patient_id) REFERENCES patients(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50) NOT NULL,
    user_id INT NULL,
    page VARCHAR(2048) NOT NULL,
    http_method VARCHAR(10) NOT NULL,
    visited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données fictives et rôles de l’application.
START TRANSACTION;

INSERT INTO privilege (id, privilege) VALUES
(1, 'Admin'),
(2, 'Client');

INSERT INTO patients (id, first_name, last_name, birth_date, phone, email) VALUES
(1, 'Sophie', 'Tremblay', '1992-03-14', '514-555-0123', 'sophie@example.com'),
(2, 'Karim', 'Benali', '1985-07-22', '514-555-0145', 'karim@example.com'),
(3, 'Emma', 'Roy', '1990-01-03', '514-555-0167', 'emma@example.com'),
(4, 'Thomas', 'Gagnon', '1978-11-17', '514-555-0134', 'thomas@example.com'),
(5, 'Leïla', 'Amari', '1995-05-09', '514-555-0178', 'leila@example.com'),
(6, 'Daniel', 'Pelletier', '1988-02-28', '514-555-0190', 'daniel@example.com');

INSERT INTO doctors (id, first_name, last_name, specialty) VALUES
(1, 'Nadia', 'Roy', 'Médecine familiale'),
(2, 'Marc', 'Gagnon', 'Médecine familiale');

INSERT INTO services (id, name, duration_minutes) VALUES
(1, 'Consultation générale', 30),
(2, 'Bilan préventif', 15),
(3, 'Suivi médical', 20);

-- Dates et heures locales du cabinet : America/Toronto.
INSERT INTO appointments (id, patient_id, doctor_id, starts_at, status, reason) VALUES
(1, 1, 1, '2026-09-30 09:00:00', 'confirmed', 'Consultation et bilan de suivi.'),
(2, 2, 2, '2026-09-30 11:30:00', 'planned', 'Rendez-vous de suivi.'),
(3, 3, 1, '2026-09-29 10:30:00', 'completed', 'Consultation générale.'),
(4, 4, 2, '2026-09-28 14:00:00', 'absent', 'Bilan préventif.'),
(5, 5, 1, '2026-09-27 09:30:00', 'cancelled', 'Suivi médical.'),
(6, 1, 2, '2026-09-15 14:00:00', 'completed', 'Suivi médical.'),
(7, 1, 1, '2026-08-20 09:30:00', 'cancelled', 'Consultation générale.');

INSERT INTO appointment_service (appointment_id, service_id) VALUES
(1, 1), (1, 2), (2, 3), (3, 1), (4, 2), (5, 3), (6, 3), (7, 1);

COMMIT;
