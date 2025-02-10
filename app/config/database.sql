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


-- TABLE EVENTS
CREATE TABLE events (
    event_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    adresse TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'refused', 'accepted') NOT NULL DEFAULT 'pending',
    eventMode ENUM('enligne', 'presentiel') NOT NULL,
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