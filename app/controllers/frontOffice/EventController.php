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
            $event->setVilleId((int)$_POST['ville_id']); // Ajout de ville_id

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

    public function getVillesByRegion()
    {
        if (isset($_GET['region_id'])) {
            $regionId = (int)$_GET['region_id'];
            $eventModel = new Event($this->pdo);
            $villes = $eventModel->fetchVillesByRegion($regionId); // Utiliser la méthode du modèle
            echo json_encode($villes);
        }
    }

    public function afficherTousLesEvenements()
    {
        $eventModel = new Event($this->pdo);

        $events = $eventModel->fetchAll();
        $categories = $eventModel->fetchCategories();
        $tags = $eventModel->fetchTags();
        // $regions = $eventModel->fetchRegions(); // Utiliser la méthode du modèle

        View::render('back/organisateur/addEvent.twig', [
            'events' => $events,
            'categories' => $categories,
            'tags' => $tags
            // 'regions' => $regions, // Passer les régions au template
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
        $regions = $eventModel->fetchRegions();

        $villes = [];
        if (isset($event['ville']) && $event['ville']['region']) {
            $villes = $eventModel->fetchVillesByRegion($event['ville']['region']);
        }

        View::render('back/organisateur/editEvent.twig', [
            'event' => $event,
            'categories' => $categories,
            'tags' => $tags,
            'regions' => $regions,
            'villes' => $villes,
        ]);
    }

    // Mettre à jour un événement
    public function updateEvent($eventId)
    {
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
                'ville_id' => (int)$_POST['ville_id'],
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
        $categoryHomePage = $eventsHomePage->fetchCategories();
        $SponsorsHomePage = $eventsHomePage->fetchAllSponsors();
        // var_dump($eventsAccepted);
        View::render('front/home.twig', [
            'eventsAccepted' => $eventsAccepted,
            'categoryHomePage' => $categoryHomePage,
            'SponsorsHomePage' => $SponsorsHomePage,
        ]);
    }

    public function displayEvents()
    {
        $eventsHomePage = new Event($this->pdo);
        $eventsAccepted = $eventsHomePage->displayEventsAccepted();
        $categoryHomePage = $eventsHomePage->fetchCategories();
        $SponsorsHomePage = $eventsHomePage->fetchAllSponsors();
        // var_dump($eventsAccepted);
        View::render('front/FindEvents.twig', [
            'eventsAccepted' => $eventsAccepted,
            'categoryHomePage' => $categoryHomePage,
            'SponsorsHomePage' => $SponsorsHomePage,
        ]);
    }


    public function searchEvents() {
        // header('Content-Type: application/json');
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (isset($data['q'])) {
                $q = $data['q'];
                $event = new Event($this->pdo);
                $dataEvents = $event->searchForEvents($q);
    
                if (is_array($dataEvents)) {
                    if (!empty($dataEvents)) {
                        echo json_encode([
                            'success' => true,
                            'data' => $dataEvents
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Aucun événement trouvé.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Search query is missing.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        }
    }
    


    public function eventDataille($id){
       
        $eventsHomePage = new Event($this->pdo);
        $eventsAccepted = $eventsHomePage->eventDetaill($id);
        View::render('front/pages/EventDataille.twig', [
            'eventsAccepted' => $eventsAccepted,
        ]);
        
    }
}



