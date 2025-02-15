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
    private ?int $ville_id = null;

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

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
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

    public function getVilleId(): ?int
    {
        return $this->ville_id;
    }

    public function setVilleId(?int $ville_id): void
    {
        $this->ville_id = $ville_id;
    }


    // Insert an event
    public function insert(array $tags, array $sponsors): int
    {
        $sql = "INSERT INTO events (title, description, image, adresse, eventMode, price, createdAt, 
                situation, capacite, lienEvent, startEventAt, endEventAt, category_id, user_id, ville_id) 
                VALUES (:title, :description, :image, :adresse, :eventMode, :price, NOW(), :situation, 
                :capacite, :lienEvent, :startEventAt, :endEventAt, :category_id, :user_id, :ville_id)";

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
            ':startEventAt' => $this->startEventAt->format('Y-m-d H:i:s'),
            ':endEventAt' => $this->endEventAt->format('Y-m-d H:i:s'),
            ':category_id' => $this->category_id,
            ':user_id' => $this->user_id,
            ':ville_id' => $this->ville_id,
        ]);

        $eventId = $this->pdo->lastInsertId();

        // Ajouter les tags à l'événement
        $this->addTagsToEvent($eventId, $tags);

        // Ajouter les sponsors à l'événement
        foreach ($sponsors as $sponsor) {
            $sponsorId = $this->handleSponsor($sponsor['name'], $sponsor['image']);
            if ($sponsorId) {
                $this->addSponsorToEvent($eventId, $sponsorId);
            }
        }

        return $eventId;
    }

    // Gérer le sponsor
    public function handleSponsor(?string $sponsorName, ?string $sponsorImagePath): ?int
    {
        if (empty($sponsorName)) {
            return null;
        }

        $sql = "SELECT sponsor_id FROM sponsors WHERE name = :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name' => $sponsorName]);
        $sponsor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sponsor) {
            return $sponsor['sponsor_id'];
        }

        $sql = "INSERT INTO sponsors (name, img) VALUES (:name, :image)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $sponsorName,
            ':image' => $sponsorImagePath,
        ]);

        return $this->pdo->lastInsertId();
    }

    // Ajouter un sponsor à un événement
    public function addSponsorToEvent(int $eventId, int $sponsorId): bool
    {
        $sql = "INSERT INTO event_sponsor (event_id, sponsor_id) VALUES (:event_id, :sponsor_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':event_id' => $eventId,
            ':sponsor_id' => $sponsorId,
        ]);
    }

    // Add tags to  event
    public function addTagsToEvent(int $eventId, array $tagIds): bool
    {
        foreach ($tagIds as $tagId) {
            $sql = "INSERT INTO events_tag (event_id, tag_id) VALUES (:event_id, :tag_id)";
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute([
                ':event_id' => $eventId,
                ':tag_id' => $tagId,
            ])) {
                return false;
            }
        }
        return true;
    }

    // Fetch all events
    public function fetchAll(int $userId): array
    {
        $sql = "SELECT e.*,u.username, c.name as category_name, c.img AS image_category 
                FROM events e
                LEFT JOIN events_tag et ON et.event_id = e.event_id
                LEFT JOIN categories c ON c.category_id = e.category_id
                LEFT JOIN users u ON u.user_id = e.user_id
                WHERE e.user_id = :user_id
                GROUP BY e.event_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all categories
    public function fetchCategories(): array
    {
        $sql = "SELECT * FROM categories";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all tags
    public function fetchTags(): array
    {
        $sql = "SELECT * FROM tags";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchVillesByRegion(int $regionId): array
    {
        if (!$regionId) {
            return [];
        }

        $sql = "SELECT * FROM ville WHERE region = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$regionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour les tags de l'événement
    public function updateEventTags(int $eventId, array $tagIds): bool
    {
        $this->deleteEventTags($eventId);

        foreach ($tagIds as $tagId) {
            $sql = "INSERT INTO events_tag (event_id, tag_id) VALUES (:event_id, :tag_id)";
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute([':event_id' => $eventId, ':tag_id' => $tagId])) {
                return false;
            }
        }

        return true;
    }

    // Récupérer les tags associés à un événement
    public function getTagsByEventId(int $eventId): array
    {
        $sql = "SELECT t.tag_id, t.name 
                    FROM tags t
                    JOIN events_tag et ON et.tag_id = t.tag_id
                    WHERE et.event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all tags
    public function fetchAllSponsors(): array
    {
        $sql = "SELECT sponsor_id,s.img As sponsor_image, s.name As name_sponsor FROM sponsors s";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $eventId): bool
    {
        // Supprimer les tags associe a l'evenement
        $this->deleteEventTags($eventId);

        $sql = "DELETE FROM events WHERE event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':event_id' => $eventId]);
    }

    public function deleteEventTags(int $eventId): bool
    {
        $sql = "DELETE FROM events_tag WHERE event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':event_id' => $eventId]);
    }

    public function displayEventsAccepted()
    {
        $sql = "SELECT e.event_id, e.title, e.description, e.image As event_image, e.price, e.startEventAt, 
                e.endEventAt,e.lienEvent, e.capacite,e.situation, e.eventMode, e.status, t.tag_id,
                GROUP_CONCAT(t.name SEPARATOR ', ') AS tags,c.category_id, c.name as category_name, 
                c.img AS image_category, 
                u.user_id, u.username, e.adresse
            FROM events e 
            LEFT JOIN events_tag et ON et.event_id = e.event_id
            LEFT JOIN tags t ON t.tag_id = et.tag_id
            LEFT JOIN users u ON u.user_id = e.user_id
            LEFT JOIN categories c ON c.category_id = e.category_id
            WHERE e.status = 'accepted'
            GROUP BY e.event_id;";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchRegions(): array
    {
        $sql = "SELECT * FROM region";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les sponsors d'un événement
    public function getSponsorsByEventId(int $eventId): array
    {
        $sql = "SELECT s.sponsor_id, s.name, s.img 
            FROM sponsors s
            JOIN event_sponsor es ON es.sponsor_id = s.sponsor_id
            WHERE es.event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $eventId): ?array
    {
        $sql = "SELECT e.*, v.id AS ville_id, v.ville AS ville_name, r.id AS region_id, r.region AS region_name
            FROM events e
            LEFT JOIN ville v ON e.ville_id = v.id
            LEFT JOIN region r ON v.region = r.id
            WHERE e.event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            return null;
        }

        // Récupérer les tags associés
        $event['tags'] = $this->getTagsByEventId($eventId);

        // Récupérer les sponsors associés
        $event['sponsors'] = $this->getSponsorsByEventId($eventId);

        // Structurer les données de la ville et de la région
        $event['ville'] = [
            'id' => $event['ville_id'],
            'ville' => $event['ville_name'],
            'region' => $event['region_id'],
        ];

        // Supprimer les clés inutiles
        unset($event['ville_id'], $event['ville_name'], $event['region_id'], $event['region_name']);

        return $event;
    }

    public function update(int $eventId, array $data): bool
    {
        $sql = "UPDATE events SET
        title = :title,
        description = :description,
        image = :image,
        adresse = :adresse,
        eventMode = :eventMode,
        price = :price,
        capacite = :capacite,
        lienEvent = :lienEvent,
        startEventAt = :startEventAt,
        endEventAt = :endEventAt,
        category_id = :category_id,
        ville_id = :ville_id
        WHERE event_id = :event_id";

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':image' => $data['image'] ?? null,
            ':adresse' => $data['adresse'],
            ':eventMode' => $data['eventMode'],
            ':price' => $data['price'],
            ':capacite' => $data['capacite'],
            ':lienEvent' => $data['lienEvent'],
            ':startEventAt' => $data['startEventAt'],
            ':endEventAt' => $data['endEventAt'],
            ':category_id' => $data['category_id'],
            ':ville_id' => $data['ville_id'],
            ':event_id' => $eventId,
        ]);

        if (!$success) {
            return false;
        }

        $this->updateEventTags($eventId, $data['tags']);

        // Mettre à jour les sponsors
        if (isset($data['sponsors'])) {
            $this->updateEventSponsors($eventId, $data['sponsors']);
        }

        return true;
    }

    // Mettre à jour les sponsors de l'événement
    public function updateEventSponsors(int $eventId, array $sponsors): bool
    {
        // Supprimer les sponsors existants
        $sql = "DELETE FROM event_sponsor WHERE event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);

        // Ajouter les nouveaux sponsors
        foreach ($sponsors as $sponsor) {
            $sponsorId = $this->handleSponsor($sponsor['name'], $sponsor['image']);
            if ($sponsorId) {
                $this->addSponsorToEvent($eventId, $sponsorId);
            }
        }

        return true;
    }

    public function removeSponsorFromEvent(int $eventId, int $sponsorId): bool
    {
        $sql = "DELETE FROM event_sponsor WHERE event_id = :event_id AND sponsor_id = :sponsor_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':event_id' => $eventId,
            ':sponsor_id' => $sponsorId,
        ]);
    }


    public function searchForEvents($q) {

        $query = " SELECT e.event_id, e.title, e.description, e.image As event_image, e.price, e.startEventAt, 
            e.endEventAt,e.lienEvent, e.capacite,e.situation, e.eventMode, e.status,c.category_id, c.name as category_name, 
            c.img AS image_category, s.sponsor_id, s.name AS sponsor_name,s.img AS sponsor_image, 
            u.user_id, u.username, e.adresse FROM events e LEFT JOIN sponsors s ON s.sponsor_id = e.sponsor_id
        LEFT JOIN events_tag et ON et.event_id = e.event_id LEFT JOIN tags t ON t.tag_id = et.tag_id
        LEFT JOIN users u ON u.user_id = e.user_id LEFT JOIN categories c ON c.category_id = e.category_id
        WHERE e.status = 'accepted' AND e.title LIKE :q GROUP BY e.event_id ORDER BY e.event_id;";
        $q = "%$q%";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':q', $q, PDO::PARAM_STR); 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function eventDetaill($id) {

        $query = "SELECT e.event_id, e.title, e.description, e.image As event_image, e.price, e.startEventAt, 
            e.endEventAt,e.lienEvent, e.capacite,e.situation, e.eventMode, e.status,c.category_id, c.name as category_name, 
            c.img AS image_category, s.sponsor_id, s.name AS sponsor_name,s.img AS sponsor_image, 
            u.user_id,u.email, u.is_verified,u.avatar, u.username, e.adresse FROM events e LEFT JOIN sponsors s ON s.sponsor_id = e.sponsor_id
        LEFT JOIN events_tag et ON et.event_id = e.event_id LEFT JOIN tags t ON t.tag_id = et.tag_id
        LEFT JOIN users u ON u.user_id = e.user_id LEFT JOIN categories c ON c.category_id = e.category_id
        WHERE e.status = 'accepted' AND e.event_id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id,); 
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}