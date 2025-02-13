<?php

namespace App\Controllers\FrontOffice;

use App\Models\Event;
use App\Config\Database;
use App\core\View;
use App\core\Validator;
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

            // Validation des données
            if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['eventMode']) || empty($_POST['capacite']) || empty($_POST['startEventAt']) || empty($_POST['endEventAt'])) {
                echo json_encode(["success" => false, "error" => "Tous les champs obligatoires doivent être remplis."]);
                return;
            }

                        
            // $errors = Validator::validateEvent($_POST);
       
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

            if ($_POST['isPaid'] === "payant") {
                $event->setPrice((float)$_POST['price']);
            } else {
                $event->setPrice(null);
            }

            // Validation de la catégorie
            if (!empty($_POST['category_id'])) {
                $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = :category_id");
                $stmt->execute([':category_id' => $_POST['category_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setCategoryId((int)$_POST['category_id']);
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $event->setImage(basename($_FILES["image"]["name"]));
                } else {
                    echo json_encode(["success" => false, "error" => "Catégorie invalide."]);
                    return;
                    throw new \Exception("Erreur lors du téléchargement de l'image.");
                }
            }

            // Validation du sponsor
            if (!empty($_POST['sponsor_id'])) {
                $stmt = $this->pdo->prepare("SELECT * FROM sponsors WHERE sponsor_id = :sponsor_id");
                $stmt->execute([':sponsor_id' => $_POST['sponsor_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setSponsorId((int)$_POST['sponsor_id']);
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

    public function afficherTousLesEvenements()
    {
        $eventModel = new Event($this->pdo);

        $events = $eventModel->fetchAll();

        $categories = $eventModel->fetchCategories();
        $sponsors = $eventModel->fetchSponsors();
        $tags = $eventModel->getAllTags();

        echo json_encode([
            'categories' => $categories['category'],
            'sponsors' => $sponsors['sponsor'],
            'tags' => $tags
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

    // Afficher le formulaire d'édition
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
   
    // Mettre à jour un événement
    public function updateEvent($eventId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === "update") {
            if (!isset($_POST['event_id']) || empty($_POST['event_id'])) {
                echo json_encode(["success" => false, "error" => "ID de l'événement manquant."]);
                return;
            }
            $event = new Event($this->pdo);
            $event->setEventId((int)$_POST['event_id']);
            // Validation des données
            if (empty($_POST['title']) || empty($_POST['description']) || empty($_POST['eventMode']) || empty($_POST['capacite']) || empty($_POST['startEventAt']) || empty($_POST['endEventAt'])) {
                echo json_encode(["success" => false, "error" => "Tous les champs obligatoires doivent être remplis."]);
                return;
            }
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

            // Validation de la catégorie
            if (!empty($_POST['category_id'])) {
                $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE category_id = :category_id");
                $stmt->execute([':category_id' => $_POST['category_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setCategoryId((int)$_POST['category_id']);
                } else {
                    echo json_encode(["success" => false, "error" => "Catégorie invalide."]);
                    return;
                }
            }

            // Validation du sponsor
            if (!empty($_POST['sponsor_id'])) {
                $stmt = $this->pdo->prepare("SELECT * FROM sponsors WHERE sponsor_id = :sponsor_id");
                $stmt->execute([':sponsor_id' => $_POST['sponsor_id']]);
                if ($stmt->fetchColumn()) {
                    $event->setSponsorId((int)$_POST['sponsor_id']);
                } else {
                    echo json_encode(["success" => false, "error" => "Sponsor invalide."]);
                    return;
                }
            }

            // Gestion de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $event->setImage(basename($_FILES["image"]["name"]));
                } else {
                    echo json_encode(["success" => false, "error" => "Erreur lors du téléchargement de l'image."]);
                    return;
                }
            } else {
                $existingEvent = $event->displayAll();
                $event->setImage($existingEvent[0]['image'] ?? null);
            }
            try {
                // Mettre à jour l'événement
                $event->update();
                // Mettre à jour les tags de l'événement
                if (!empty($_POST['tags'])) {
                    $event->updateEventTags((int)$_POST['event_id'], $_POST['tags']);
                }
                echo json_encode(["success" => true, "message" => "Événement mis à jour avec succès."]);
            } catch (\Exception $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
        }
    }


    public function deleteEvent()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $event = new Event($this->pdo);
        try {
            $eventId = (int)$_POST['id'];
            $event->setEventId($eventId);

            // Récupérer l'image de l'événement
            $existingEvent = $event->displayAll();
            if (empty($existingEvent)) {
                echo json_encode(['success' => false, 'error' => 'Événement non trouvé.']);
                return;
            }

            $imagePath = __DIR__ . "/../../../public/assets/images/" . ($existingEvent[0]['image'] ?? '');

            // Supprimer l'événement
            if ($event->delete($eventId)) {
                // Supprimer l'image si elle existe
                if (!empty($existingEvent[0]['image']) && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Événement non trouvé ou déjà supprimé.']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Requête invalide.']);
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventModel = new Event($this->pdo);

            $currentEvent = $eventModel->findById($eventId);

            // $errors = Validator::validateEvent($_POST);

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
                'sponsor_image_path' => $currentEvent['sponsor_image'] ?? null,
                'startEventAt' => $_POST['startEventAt'],
                'endEventAt' => $_POST['endEventAt'],
                'image' => $currentEvent['image'] ?? null, 
            ];

            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . "/../../../public/assets/images/"; 
                $uploadFile = $uploadDir . basename($_FILES['event_image']['name']);

                if (!empty($currentEvent['image'])) {
                    $oldImagePath = $uploadDir . $currentEvent['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                if (move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadFile)) {
                    $data['image'] = basename($_FILES['event_image']['name']); 
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload de l\'image.']);
                    return;
                }
            }

            $success = $eventModel->update($eventId, $data);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Événement mis à jour avec succès.']);
                header('Location: /events');
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
        }
    }

    public function displayEventsAcceptedHome()
    {
        $eventsHomePage = new Event($this->pdo);
        $eventsAccepted = $eventsHomePage->displayEventsAccepted();
        // var_dump($eventsAccepted);
        View::render('front/home.twig', [
            'eventsAccepted' => $eventsAccepted,
        ]);
    }



}