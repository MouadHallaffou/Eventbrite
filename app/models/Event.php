<?php

namespace App\Models;

use PDO;
use DateTime;

class Event
{
    private ?int $event_id = null;
    private ?int $user_id;
    private string $title;
    private ?string $image = null;
    private string $description;
    private ?string $adresse = null;
    private string $eventMode;
    private ?float $price = null;
    private DateTime $createdAt;
    private string $situation;
    private int $capacite;
    private ?string $lienEvent = null;
    private DateTime $startEventAt;
    private DateTime $endEventAt;
    private ?int $sponsor_id = null;
    private ?int $category_id = null;
    private PDO $pdo;

    public function __construct(PDO $conn)
    {
        $this->pdo = $conn;
        $this->createdAt = new DateTime();
        $this->situation = 'encours';
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->event_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getEventMode(): string
    {
        return $this->eventMode;
    }

    public function setEventMode(string $eventMode): void
    {
        $this->eventMode = $eventMode;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getSituation(): string
    {
        return $this->situation;
    }

    public function setSituation(string $situation): void
    {
        $this->situation = $situation;
    }

    public function getCapacite(): int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): void
    {
        $this->capacite = $capacite;
    }

    public function getLienEvent(): ?string
    {
        return $this->lienEvent;
    }

    public function setLienEvent(?string $lienEvent): void
    {
        $this->lienEvent = $lienEvent;
    }

    public function getStartEventAt(): DateTime
    {
        return $this->startEventAt;
    }

    public function setStartEventAt(DateTime $startEventAt): void
    {
        $this->startEventAt = $startEventAt;
    }

    public function getEndEventAt(): DateTime
    {
        return $this->endEventAt;
    }

    public function setEndEventAt(DateTime $endEventAt): void
    {
        $this->endEventAt = $endEventAt;
    }

    public function getSponsorId(): ?int
    {
        return $this->sponsor_id;
    }

    public function setSponsorId(?int $sponsor_id): void
    {
        $this->sponsor_id = $sponsor_id;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function setUserId(?int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function setEventId(?int $event_id)
    {
        $this->event_id = $event_id;
    }

    
    public function insert()
    {
        $sql = "INSERT INTO events (title, description, image, adresse, eventMode, price, createdAt, situation, capacite, lienEvent, 
                startEventAt, endEventAt, sponsor_id, category_id, user_id) VALUES (:title, :description, :image, :adresse, :eventMode, 
                :price, NOW(), :situation, :capacite, :lienEvent, :startEventAt, :endEventAt, :sponsor_id, :category_id, :user_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':image' => $this->image,
            ':adresse' => $this->adresse,
            ':eventMode' => $this->eventMode,
            ':price' => $this->price,
            ':situation' => $this->situation,
            ':capacite' => $this->capacite,
            ':lienEvent' => $this->lienEvent,
            ':startEventAt' => $this->startEventAt->format('Y-m-d'),
            ':endEventAt' => $this->endEventAt->format('Y-m-d'),
            ':sponsor_id' => $this->sponsor_id,
            ':category_id' => $this->category_id,
            ':user_id' => $this->user_id
        ]);
        return $this->pdo->lastInsertId();
    }

    public function fetchCategories()
    {
        $stmtCategories = $this->pdo->query("SELECT category_id, name FROM categories;");
        return ['category' => $stmtCategories->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function fetchSponsors()
    {
        $stmtSponsors = $this->pdo->query("SELECT sponsor_id, name FROM sponsors;");
        return ['sponsor' => $stmtSponsors->fetchAll(PDO::FETCH_ASSOC)];
    }


    public function update()
    {
        $sql = "UPDATE events 
                SET title = :title, 
                    description = :description, 
                    adresse = :adresse, 
                    eventMode = :eventMode, 
                    price = :price, 
                    situation = :situation, 
                    capacite = :capacite, 
                    lienEvent = :lienEvent, 
                    startEventAt = :startEventAt, 
                    endEventAt = :endEventAt, 
                    sponsor_id = :sponsor_id, 
                    category_id = :category_id,
                    image = :image 
                    WHERE event_id = :event_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':adresse' => $this->adresse,
            ':eventMode' => $this->eventMode,
            ':price' => $this->price,
            ':situation' => $this->situation,
            ':capacite' => $this->capacite,
            ':lienEvent' => $this->lienEvent,
            ':startEventAt' => $this->startEventAt->format('Y-m-d H:i:s'),
            ':endEventAt' => $this->endEventAt->format('Y-m-d H:i:s'),
            ':sponsor_id' => $this->sponsor_id,
            ':category_id' => $this->category_id,
            ':image' => $this->image,
            ':event_id' => $this->event_id
        ]);
    }


    public function displayAll($eventId = null)
    {
        $sql = "SELECT e.event_id, u.user_id, u.username, c.name AS category_name, c.img AS category_img, 
                s.name AS sponsor_name, s.img AS sponsor_img, e.eventMode, e.title, e.description, 
                e.price, e.endEventAt, e.image, c.category_id, e.createdAt, s.sponsor_id 
                FROM events e
                LEFT JOIN users u ON u.user_id = e.user_id
                LEFT JOIN sponsors s ON s.sponsor_id = e.sponsor_id
                LEFT JOIN categories c ON c.category_id = e.category_id";

        if ($eventId) {
            $sql .= " WHERE e.event_id = :event_id";
        }

        $stmt = $this->pdo->prepare($sql);
        if ($eventId) {
            $stmt->execute([':event_id' => $eventId]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function delete($event_id)
    {
        $sql = "DELETE FROM events WHERE event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':event_id' => $event_id]);
        return $stmt->rowCount();
    }
}