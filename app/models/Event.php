<?php
namespace App\Models;

use PDO;
use DateTime;
use App\Config\Database;

class Event {
    private ?int $id = null;
    private string $title;
    private string $description;
    private string $adresse;
    private string $status;
    private string $eventMode;
    private float $price;
    private DateTime $createdAt;
    private string $situation;
    private int $capacite;
    private ?string $lienEvent;
    private DateTime $startEventAt;
    private DateTime $endEventAt;
    private PDO $pdo;

    public function __construct(PDO $conn, string $title, string $description, string $adresse, string $status, 
                                string $eventMode, float $price, string $situation, int $capacite, 
                                ?string $lienEvent, string $startEventAt, string $endEventAt)
    {
        $this->pdo = $conn;
        $this->title = $title;
        $this->description = $description;
        $this->adresse = $adresse;
        $this->status = $status;
        $this->eventMode = $eventMode;
        $this->price = $price;
        $this->situation = $situation;
        $this->capacite = $capacite;
        $this->lienEvent = $lienEvent;
        $this->startEventAt = new DateTime($startEventAt);
        $this->endEventAt = new DateTime($endEventAt);
        $this->createdAt = new DateTime();
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getEventMode(): string {
        return $this->eventMode;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getSituation(): string {
        return $this->situation;
    }

    public function getCapacite(): int {
        return $this->capacite;
    }

    public function getLienEvent(): ?string {
        return $this->lienEvent;
    }

    public function getStartEventAt(): DateTime {
        return $this->startEventAt;
    }

    public function getEndEventAt(): DateTime {
        return $this->endEventAt;
    }

    // Setters
    public function setTitle(string $title): void {
        if (strlen($title) < 3) {
            throw new \Exception("Le titre doit contenir au moins 3 caractères.");
        }
        $this->title = $title;
    }

    public function setDescription(string $description): void {
        if (strlen($description) < 10) {
            throw new \Exception("La description doit contenir au moins 10 caractères.");
        }
        $this->description = $description;
    }

    public function setStatus(string $status): void {
        $validStatuses = ['pending', 'refused', 'accepted'];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception("Statut invalide.");
        }
        $this->status = $status;
    }

    public function insert(): int {
        $sql = "INSERT INTO events 
                (title, description, adresse, status, eventMode, price, createdAt, situation, 
                 capacite, lienEvent, startEventAt, endEventAt) 
                VALUES 
                (:title, :description, :adresse, :status, :eventMode, :price, NOW(), :situation, 
                 :capacite, :lienEvent, :startEventAt, :endEventAt)";
        
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':adresse' => $this->adresse,
            ':status' => $this->status,
            ':eventMode' => $this->eventMode,
            ':price' => $this->price,
            ':situation' => $this->situation,
            ':capacite' => $this->capacite,
            ':lienEvent' => $this->lienEvent,
            ':startEventAt' => $this->startEventAt->format('Y-m-d'),
            ':endEventAt' => $this->endEventAt->format('Y-m-d')
        ]);

        $this->id = $this->pdo->lastInsertId();
        return $this->id;
    }
}
