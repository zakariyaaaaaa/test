CREATE DATABASE Gestion_reservation;
USE Gestion_reservation.
CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
   password  VARCHAR(100)
);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    date_event DATE,
    nbPlaces INT,
    price DECIMAL(8,2),
    location VARCHAR(255)
);
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    reservation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

INSERT INTO events (title, date_event, nbPlaces, price, location) VALUES
('Festival Musique Casablanca', '2026-05-10', 20, 150.00, 'Casablanca'),
('Conférence Tech Rabat', '2026-05-15', 20, 200.00, 'Rabat'),
('Workshop Marketing', '2026-05-20', 20, 120.00, 'Marrakech'),
('Concert Rap Maroc', '2026-06-01', 20, 180.00, 'Tanger'),
('Salon Entrepreneuriat', '2026-06-10', 20, 100.00, 'Agadir');