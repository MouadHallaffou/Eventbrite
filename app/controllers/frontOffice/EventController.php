<?php

namespace App\controllers\frontOffice;

require realpath(__DIR__ . '/../../../vendor/autoload.php');

use App\Models\Event;
use App\Config\Database;
use App\core\Controller;
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
                $target_dir = "/public/assets/images/";
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
                    "redirect_url" => "Event.twig"
                ]);
                exit();
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
        echo Controller::render('organisateur/addEvent.twig', [
            'categories' => $categories['category'],
            'sponsors' => $sponsors['sponsor']
        ]);
    }


    public function editEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === "update") {
            $event = new Event($this->pdo);
            // Récupérer l'ID de l'événement à mettre à jour
            $event->setEventId((int)$_POST['event_id']);

            $event->setTitle($_POST['title']);
            $event->setDescription($_POST['description']);
            $event->setEventMode($_POST['eventMode']);
            $event->setCapacite((int)$_POST['capacite']);
            $event->setAdresse($_POST['adresse']);
            $event->setLienEvent($_POST['lienEvent']);
            $event->setPrice($_POST['isPaid'] === "payant" ? (float)$_POST['price'] : null);
            $event->setSponsorId(!empty($_POST['sponsor_id']) ? (int)$_POST['sponsor_id'] : null);
            $event->setCategoryId(!empty($_POST['category_id']) ? (int)$_POST['category_id'] : null);
            $event->setStartEventAt(new \DateTime($_POST['startEventAt']));
            $event->setEndEventAt(new \DateTime($_POST['endEventAt']));

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = "public/asets/images";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                $event->setImage($target_file);
            }

            try {
                $event->update();
                echo json_encode(["success" => true]);
            } catch (\Exception $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
        }
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


    public function deleteEvent($event_id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $event = new Event($this->pdo);
            try {
                $event->setEventId((int)$_POST['id']);
                $event->delete($event);
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
}

$event = new EventController();
$event->createEvent();
$event->afficheEvents();
$event->displayEventForm();
