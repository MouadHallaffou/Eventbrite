<?php
namespace App\Models;

use PDO;
use DateTime;

class Event {
    private ?int $id = null;
    private string $title;
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
    }

    // Getters et Setters

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void {
        $this->adresse = $adresse;
    }

    public function getEventMode(): string {
        return $this->eventMode;
    }

    public function setEventMode(string $eventMode): void {
        $this->eventMode = $eventMode;
    }

    public function getPrice(): ?float {
        return $this->price;
    }

    public function setPrice(?float $price): void {
        $this->price = $price;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getSituation(): string {
        return $this->situation;
    }

    public function setSituation(string $situation): void {
        $this->situation = $situation;
    }

    public function getCapacite(): int {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): void {
        $this->capacite = $capacite;
    }

    public function getLienEvent(): ?string {
        return $this->lienEvent;
    }

    public function setLienEvent(?string $lienEvent): void {
        $this->lienEvent = $lienEvent;
    }

    public function getStartEventAt(): DateTime {
        return $this->startEventAt;
    }

    public function setStartEventAt(DateTime $startEventAt): void {
        $this->startEventAt = $startEventAt;
    }

    public function getEndEventAt(): DateTime {
        return $this->endEventAt;
    }

    public function setEndEventAt(DateTime $endEventAt): void {
        $this->endEventAt = $endEventAt;
    }

    public function getSponsorId(): ?int {
        return $this->sponsor_id;
    }

    public function setSponsorId(?int $sponsor_id): void {
        $this->sponsor_id = $sponsor_id;
    }

    public function getCategoryId(): ?int {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): void {
        $this->category_id = $category_id;
    }

    // Méthode d'insertion d'un événement
    public function insert(){
        $sql = "INSERT INTO events 
                (title, description, adresse, eventMode, price, createdAt, situation, capacite, lienEvent, startEventAt, endEventAt, sponsor_id, category_id) 
                VALUES 
                (:title, :description, :adresse, :eventMode, :price, NOW(), :situation, :capacite, :lienEvent, :startEventAt, :endEventAt, :sponsor_id, :category_id)";
        
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':title' => $this->title,
            ':description' => $this->description,
            ':adresse' => $this->adresse,
            ':eventMode' => $this->eventMode,
            ':price' => $this->price ?? null,
            ':situation' => $this->situation,
            ':capacite' => $this->capacite,
            ':lienEvent' => $this->lienEvent,
            ':startEventAt' => $this->startEventAt->format('Y-m-d'),
            ':endEventAt' => $this->endEventAt->format('Y-m-d'),
            ':sponsor_id' => $this->sponsor_id,
            ':category_id' => $this->category_id
        ]);

        return $this->pdo->lastInsertId();
    }

    
    public function fetchCategoriesAndSponsors(){
        // Récupérer les catégories
        $stmtCategories = $this->pdo->query("SELECT id, name FROM categories");
        $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les sponsors
        $stmtSponsors = $this->pdo->query("SELECT id, name FROM sponsors");
        $sponsors = $stmtSponsors->fetchAll(PDO::FETCH_ASSOC);

        return [
            'categories' => $categories,
            'sponsors' => $sponsors
        ];
    }
}
