-- Supprime la base de données si elle existe déjà
DROP DATABASE IF EXISTS Eventbrite;
CREATE DATABASE Eventbrite;
USE Eventbrite;

-- TABLE ROLES
CREATE TABLE roles (
    role_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name_role VARCHAR(100) NOT NULL UNIQUE
);

-- TABLE USERS
CREATE TABLE users (
    user_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL, 
    gender ENUM('homme', 'femme') NOT NULL,
    STATUS ENUM('banned','accepted') NOT NULL DEFAULT 'accepted',
    is_verified ENUM('yes','no') NOT NULL DEFAULT 'no',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- TABLE PIVOT USERS ↔ ROLES
CREATE TABLE user_roles (
    user_id BIGINT NOT NULL,
    role_id BIGINT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE ON UPDATE CASCADE
);


-- TABLE CATEGORIES
CREATE TABLE categories (
    category_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    img VARCHAR(255) DEFAULT NULL
);

-- TABLE SPONSORS
CREATE TABLE sponsors (
    sponsor_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    img VARCHAR(255) DEFAULT NULL
);

-- TABLE TAGS
CREATE TABLE tags (
    tag_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(250) NOT NULL,
    PRIMARY KEY (tag_id)
);


-- TABLE events
CREATE TABLE events (
    event_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    adresse TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'refused', 'accepted') NOT NULL DEFAULT 'pending',
    eventMode ENUM('en ligne', 'presentiel') NOT NULL,
    price FLOAT DEFAULT NULL CHECK (price >= 0),
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    situation ENUM('completed', 'annulled', 'encours') NOT NULL DEFAULT 'encours',
    capacite BIGINT NOT NULL CHECK (capacite > 0),
    lienEvent VARCHAR(255) DEFAULT NULL,
    startEventAt DATE NOT NULL, 
    endEventAt DATE NOT NULL,
    sponsor_id BIGINT DEFAULT NULL,
    category_id BIGINT DEFAULT NULL,
    user_id BIGINT NOT NULL,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(sponsor_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE events_TAGS
CREATE TABLE events_tag (
    event_id BIGINT NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (event_id, tag_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE NOTIFICATIONS
CREATE TABLE notifications (
    notification_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    content TEXT NOT NULL,
    dateEnvoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('lu', 'nonlu') NOT NULL DEFAULT 'nonlu',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE TICKETS
CREATE TABLE tickets (
    ticket_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    generateDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE FEEDBACK
CREATE TABLE feedback (
    feedback_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    content TEXT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TABLE ORDERS
CREATE TABLE orders (
    order_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    participant_id BIGINT NOT NULL,
    totalAmount FLOAT NOT NULL CHECK (totalAmount >= 0),
    status ENUM('en attente', 'paye', 'annule', 'rembourse') NOT NULL DEFAULT 'en attente',
    paymentMethod ENUM('carte', 'PayPal') NOT NULL,
    transaction_id VARCHAR(100) UNIQUE DEFAULT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (participant_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- TABLE ORDER DETAILS
CREATE TABLE orderdetails (
    orderdetail_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT NOT NULL,
    event_id BIGINT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    unitPrice FLOAT NOT NULL CHECK (unitPrice >= 0),
    subtotal FLOAT NOT NULL CHECK (subtotal >= 0),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- INDEXATION POUR OPTIMISATION
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_event_title ON events(title);
CREATE INDEX idx_event_status ON events(status);
CREATE INDEX idx_order_status ON orders(STATUS);




INSERT INTO roles (name_role) VALUES
('Admin'),
('Organizer'),
('Participant');

INSERT INTO users (username, email, password, avatar, gender, STATUS, is_verified) VALUES
('john_doe', 'john.doe@example.com', 'password123', 'avatar1.jpg', 'homme', 'accepted', 'yes'),
('jane_smith', 'jane.smith@example.com', 'password456', 'avatar2.jpg', 'femme', 'accepted', 'yes'),
('alice_wonder', 'alice.wonder@example.com', 'password789', 'avatar3.jpg', 'femme', 'accepted', 'no');

INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1), -- john_doe is an Admin
(2, 2), -- jane_smith is an Organizer
(3, 3); -- alice_wonder is a Participant

INSERT INTO categories (name, img) VALUES
('Music', 'music.jpg'),
('Technology', 'tech.jpg'),
('Sports', 'sports.jpg');

INSERT INTO sponsors (name, img) VALUES
('TechCorp', 'techcorp.jpg'),
('MusicWorld', 'musicworld.jpg'),
('Sportify', 'sportify.jpg');


INSERT INTO tags (name) VALUES
('Concert'),
('Conference'),
('Workshop'),
('Marathon');


INSERT INTO events (title, description, adresse, image, status, eventMode, price, capacite, lienEvent, startEventAt, endEventAt, sponsor_id, category_id, user_id) VALUES
('Tech Conference 2023', 'Annual tech conference for developers.', '123 Tech Street, San Francisco', 'tech_conf.jpg', 'accepted', 'presentiel', 100.00, 500, NULL, '2023-12-15', '2023-12-17', 1, 2, 2),
('Summer Music Festival', 'Enjoy the best music of the year.', '456 Music Avenue, Los Angeles', 'music_fest.jpg', 'pending', 'presentiel', 50.00, 1000, NULL, '2023-07-20', '2023-07-22', 2, 1, 2),
('City Marathon', 'Run through the city and stay fit.', '789 Sports Road, New York', 'marathon.jpg', 'accepted', 'presentiel', 20.00, 2000, NULL, '2023-09-10', '2023-09-10', 3, 3, 2);

INSERT INTO events_tag (event_id, tag_id) VALUES
(1, 2), -- Tech Conference is a Conference
(2, 1), -- Summer Music Festival is a Concert
(3, 4); -- City Marathon is a Marathon

INSERT INTO notifications (user_id, content, status) VALUES
(1, 'Your admin account has been created.', 'lu'),
(2, 'Your event Tech Conference 2023 has been accepted.', 'nonlu'),
(3, 'Welcome to Eventbrite! Verify your email to get started.', 'nonlu');

INSERT INTO tickets (event_id, user_id) VALUES
(1, 3), -- alice_wonder bought a ticket for Tech Conference 2023
(2, 3); -- alice_wonder bought a ticket for Summer Music Festival

INSERT INTO feedback (event_id, user_id, content) VALUES
(1, 3, 'Great event! Learned a lot about new technologies.'),
(2, 3, 'Amazing music and atmosphere!');

INSERT INTO orders (participant_id, totalAmount, status, paymentMethod, transaction_id) VALUES
(3, 100.00, 'paye', 'carte', 'txn_123456'),
(3, 50.00, 'paye', 'PayPal', 'txn_789012');


INSERT INTO orderdetails (order_id, event_id, quantity, unitPrice, subtotal) VALUES
(1, 1, 1, 100.00, 100.00), -- Order for Tech Conference 2023
(2, 2, 1, 50.00, 50.00); -- Order for Summer Music Festival