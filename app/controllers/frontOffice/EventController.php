<?php

namespace App\Controllers\FrontOffice;

use App\Models\Event;
use App\Config\Database;
use App\core\View;
use PDO;

class EventController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstanse()->getConnection();
    }

    public function createEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === "insert") {
            $event = new Event($this->pdo);

            // Set event properties
            $event->setTitle($_POST['title']);
            $event->setDescription($_POST['description']);
            $event->setEventMode($_POST['eventMode']);
            $event->setCapacite((int)$_POST['capacite']);
            $event->setStartEventAt(new \DateTime($_POST['startEventAt']));
            $event->setEndEventAt(new \DateTime($_POST['endEventAt']));
            $event->setPrice($_POST['isPaid'] === "payant" ? (float)$_POST['price'] : null);
            $event->setCategoryId((int)$_POST['category_id']);
            $event->setUserId((int)$_POST['user_id']);

            // Handle address and link
            if ($_POST['eventMode'] === "presentiel") {
                $event->setAdresse($_POST['adresse']);
                $event->setLienEvent(null);
            } else {
                $event->setLienEvent($_POST['lienEvent']);
                $event->setAdresse(null);
            }

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $event->setImage(basename($_FILES["image"]["name"]));
                } else {
                    throw new \Exception("Erreur lors du téléchargement de l'image.");
                }
            }

            // Handle sponsor
            $sponsorName = $_POST['sponsor_name'] ?? null;
            $sponsorImage = null;
            if (isset($_FILES['image_sponsor']) && $_FILES['image_sponsor']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image_sponsor"]["name"]);
                if (move_uploaded_file($_FILES["image_sponsor"]["tmp_name"], $target_file)) {
                    $sponsorImage = basename($_FILES["image_sponsor"]["name"]);
                } else {
                    throw new \Exception("Erreur lors du téléchargement de l'image du sponsor.");
                }
            }

            // Insert event
            $tags = $_POST['tags'] ?? [];
            $eventId = $event->insert($tags, $sponsorName, $sponsorImage);

            // Return success response
            echo json_encode([
                "success" => true,
                "event_id" => $eventId,
                "redirect_url" => "/events"
            ]);
        }
    }

    public function fetchAllEvents()
    {
        $event = new Event($this->pdo);
        $events = $event->fetchAll();
        return $events;
    }

    public function afficherTousLesEvenements()
    {
        $eventModel = new Event($this->pdo);

        $events = $eventModel->fetchAll();

        $categories = $eventModel->fetchCategories();

        $tags = $eventModel->fetchTags();

        View::render('back/organisateur/addEvent.twig', [
            'events' => $events,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function deleteEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
            $eventId = (int)$_POST['event_id'];
            $eventModel = new Event($this->pdo);

            try {
                // Supprimer l'événement
                $success = $eventModel->delete($eventId);

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Événement supprimé avec succès.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Événement non trouvé ou déjà supprimé.']);
                }
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Requête invalide.']);
        }
    }

    public function editEvent($eventId)
    {
        $eventModel = new Event($this->pdo);

        $event = $eventModel->findById($eventId);

        if (!$event) {
            echo "Événement non trouvé.";
            return;
        }

        $categories = $eventModel->fetchCategories();
        $tags = $eventModel->fetchTags();

        View::render('back/organisateur/editEvent.twig', [
            'event' => $event,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    
    public function updateEvent($eventId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventModel = new Event($this->pdo);

            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'eventMode' => $_POST['eventMode'],
                'adresse' => $_POST['adresse'] ?? null,
                'lienEvent' => $_POST['lienEvent'] ?? null,
                'price' => $_POST['isPaid'] === 'payant' ? (float)$_POST['price'] : null,
                'capacite' => (int)$_POST['capacite'],
                'category_id' => (int)$_POST['category_id'],
                'tags' => $_POST['tags'] ?? [],
                'sponsor_name' => $_POST['sponsor_name'] ?? null,
                'sponsor_image' => $_FILES['image_sponsor'] ?? null,
                'startEventAt' => $_POST['startEventAt'],
                'endEventAt' => $_POST['endEventAt'],
            ];

            if (isset($_FILES['image_sponsor']) && $_FILES['image_sponsor']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/sponsors/'; 
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $uploadFile = $uploadDir . basename($_FILES['image_sponsor']['name']);

                if (move_uploaded_file($_FILES['image_sponsor']['tmp_name'], $uploadFile)) {
                    $data['sponsor_image_path'] = $uploadFile; 
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload de l\'image.']);
                    return;
                }
            }

            $success = $eventModel->update($eventId, $data);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Événement mis à jour avec succès.']);
                header('Location: /addEvent/' . $eventId);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
        }
    }

}