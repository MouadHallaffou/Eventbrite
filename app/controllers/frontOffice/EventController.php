<?php

namespace App\controllers\frontOffice;

require realpath(__DIR__ . '/../../../vendor/autoload.php');

use App\Models\Event;
use App\Config\Database;
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

            $event->setTitle($_POST['title']);
            $event->setDescription($_POST['description']);
            $event->setEventMode($_POST['eventMode']);
            $event->setCapacite((int)$_POST['capacite']);
            $event->setStartEventAt(new \DateTime($_POST['startEventAt']));
            $event->setEndEventAt(new \DateTime($_POST['endEventAt']));

            if ($_POST['eventMode'] === "presentiel") {
                $event->setAdresse($_POST['adresse']);
                $event->setLienEvent(null);
            } else {
                $event->setLienEvent($_POST['lienEvent']);
                $event->setAdresse(null);
            }

            if ($_POST['isPaid'] === "payant") {
                $event->setPrice((float)$_POST['price']);
            } else {
                $event->setPrice(null);
            }

            if ($_POST['category_id']) {
                $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = :category_id");
                $stmt->execute([':category_id' => $_POST['category_id']]);
                $categoryExists = $stmt->fetchColumn();

                if ($categoryExists) {
                    $event->setCategoryId((int)$_POST['category_id']);
                } else {
                    $event->setCategoryId(null);
                }
            }

            if ($_POST['sponsor_id']) {
                $stmt = $this->pdo->prepare("SELECT * FROM sponsors WHERE sponsor_id = :sponsor_id");
                $stmt->execute([':sponsor_id' => $_POST['sponsor_id']]);
                $sponsorExists = $stmt->fetchColumn();

                if ($sponsorExists) {
                    $event->setSponsorId((int)$_POST['sponsor_id']);
                } else {
                    $event->setSponsorId(null);
                }
            }

            if (!empty($_POST['user_id'])) {
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $_POST['user_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setUserId((int)$_POST['user_id']);
                } else {
                    echo json_encode(["success" => false, "error" => "User ID does not exist."]);
                    return;
                }
            } else {
                echo json_encode(["success" => false, "error" => "User ID is required."]);
                return;
            }

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                $event->setImage($target_file);
            } else {
                $event->setImage(null);
            }

            try {
                $eventId = $event->insert();
                echo json_encode([
                    "success" => true,
                    "event_id" => $eventId,
                    "redirect_url" => "/app/views/back/organisateur/Events.twig"
                ]);
            } catch (\Exception $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
        }
    }

    public function displayEventForm()
    {
        $eventModel = new Event($this->pdo);
        $categories = $eventModel->fetchCategories();
        $sponsors = $eventModel->fetchSponsors();
        echo json_encode([
            'categories' => $categories['category'],
            'sponsors' => $sponsors['sponsor']
        ]);
    }

    public function afficheEvents()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'view') {
            $event = new Event($this->pdo);
            $dataEvents = $event->displayAll();

            if ($dataEvents) {
                echo json_encode([
                    'success' => true,
                    'data' => $dataEvents
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Aucun événement trouvé.']);
            }
        }
    }

    public function editEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === "update") {
            if (!isset($_POST['event_id']) || empty($_POST['event_id'])) {
                echo json_encode(["success" => false, "error" => "ID de l'événement manquant."]);
                return;
            }

            $event = new Event($this->pdo);
            $event->setEventId((int)$_POST['event_id']);

            $event->setTitle($_POST['title']);
            $event->setDescription($_POST['description']);
            $event->setEventMode($_POST['eventMode']);
            $event->setCapacite((int)$_POST['capacite']);
            $event->setStartEventAt(new \DateTime($_POST['startEventAt']));
            $event->setEndEventAt(new \DateTime($_POST['endEventAt']));

            if ($_POST['eventMode'] === "presentiel") {
                $event->setAdresse($_POST['adresse']);
                $event->setLienEvent(null);
            } else {
                $event->setLienEvent($_POST['lienEvent']);
                $event->setAdresse(null);
            }

            if ($_POST['isPaid'] === "payant") {
                $event->setPrice((float)$_POST['price']);
            } else {
                $event->setPrice(null);
            }

            // mettre a jour la catégorie
            if ($_POST['category_id']) {
                $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = :category_id");
                $stmt->execute([':category_id' => $_POST['category_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setCategoryId((int)$_POST['category_id']);
                } else {
                    $event->setCategoryId(null);
                }
            }

            // mettre a jour le sponsor
            if ($_POST['sponsor_id']) {
                $stmt = $this->pdo->prepare("SELECT * FROM sponsors WHERE sponsor_id = :sponsor_id");
                $stmt->execute([':sponsor_id' => $_POST['sponsor_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setSponsorId((int)$_POST['sponsor_id']);
                } else {
                    $event->setSponsorId(null);
                }
            }

            // Gestion de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                $event->setImage($target_file);
            } else {
                $existingEvent = $event->displayAll(); 
                $event->setImage($existingEvent[0]['image'] ?? null);
            }

            try {
                $event->update();
                echo json_encode(["success" => true, "message" => "Événement mis à jour avec succès."]);
            } catch (\Exception $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
        }
    }


    public function deleteEvent($event_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $event = new Event($this->pdo);
            try {
                $event->setEventId((int)$_POST['id']);

                $existingEvent = $event->displayAll();
                $imagePath = __DIR__ . "/../../../public/assets/images/" . ($existingEvent[0]['image'] ?? '');

                $event->delete($event_id);

                // Supprimer l'image 
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }

    public function annuledEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $event = new Event($this->pdo);
            try {
                $event->setEventId((int)$_POST['id']);
                $event->setSituation('annulled');
                $event->update();
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }

    public function fetchEvent()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetchEvent') {
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            echo json_encode(["success" => false, "message" => "ID de l'événement manquant."]);
            return;
        }

        $eventId = (int)$_POST['id'];
        $event = new Event($this->pdo);
        $event->setEventId($eventId);

        try {
            $eventData = $event->displayAll(); 
            if ($eventData) {
                echo json_encode(["success" => true, "data" => $eventData[0]]);
            } else {
                echo json_encode(["success" => false, "message" => "Événement non trouvé."]);
            }
        } catch (\Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }
}

}

$eventController = new EventController();
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'insert':
            $eventController->createEvent();
            break;
        case 'update':
            $eventController->editEvent();
            break;
        case 'view':
            $eventController->afficheEvents();
            break;
        case 'fetchFormData':
            $eventController->displayEventForm();
            break;
    }
}

