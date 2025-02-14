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

            // Assigner les valeurs du formulaire
            $event->setTitle($_POST['title']);
            $event->setDescription($_POST['description']);
            $event->setEventMode($_POST['eventMode']);
            $event->setCapacite((int)$_POST['capacite']);
            $event->setStartEventAt(new \DateTime($_POST['startEventAt']));
            $event->setEndEventAt(new \DateTime($_POST['endEventAt']));
            $event->setPrice($_POST['isPaid'] === "payant" ? (float)$_POST['price'] : null);
            $event->setCategoryId((int)$_POST['category_id']);
            $event->setUserId((int)$_POST['user_id']);
            $event->setVilleId((int)$_POST['ville_id']);

            // Gérer l'adresse et le lien
            if ($_POST['eventMode'] === "presentiel") {
                $event->setAdresse($_POST['adresse']);
                $event->setLienEvent(null);
            } else {
                $event->setLienEvent($_POST['lienEvent']);
                $event->setAdresse(null);
            }

            // Gérer l'upload de l'image de l'événement
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $target_dir = __DIR__ . "/../../../public/assets/images/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $event->setImage(basename($_FILES["image"]["name"]));
                } else {
                    throw new \Exception("Erreur lors du téléchargement de l'image.");
                }
            }

            // Gérer les sponsors dynamiques
            $sponsors = [];
            if (isset($_POST['sponsors']) && is_array($_POST['sponsors'])) {
                foreach ($_POST['sponsors'] as $index => $sponsorData) {
                    $sponsorName = $sponsorData['name'] ?? null;
                    $sponsorImage = null;

                    // Gérer l'upload de l'image du sponsor
                    if (isset($_FILES['sponsors']['tmp_name'][$index]['image']) && $_FILES['sponsors']['error'][$index]['image'] === UPLOAD_ERR_OK) {
                        $target_dir = __DIR__ . "/../../../public/assets/images/";
                        $target_file = $target_dir . basename($_FILES["sponsors"]["name"][$index]["image"]);
                        if (move_uploaded_file($_FILES["sponsors"]["tmp_name"][$index]["image"], $target_file)) {
                            $sponsorImage = basename($_FILES["sponsors"]["name"][$index]["image"]);
                        } else {
                            throw new \Exception("Erreur lors du téléchargement de l'image du sponsor.");
                        }
                    }

                    // Ajouter le sponsor à la liste
                    if ($sponsorName) {
                        $sponsors[] = [
                            'name' => $sponsorName,
                            'image' => $sponsorImage,
                        ];
                    }
                }
            }

            // Insérer l'événement et les sponsors
            $tags = $_POST['tags'] ?? [];
            $eventId = $event->insert($tags, $sponsors);

            // Retourner une réponse JSON
            header('Content-Type: application/json');
            echo json_encode([
                "success" => true,
                "event_id" => $eventId,
                "redirect_url" => "/addEvent"
            ]);
            exit;
        }
    }

    /**
     * Récupère les villes par région.
     */
    public function getVillesByRegion()
    {
        if (isset($_GET['region_id'])) {
            $regionId = (int)$_GET['region_id'];
            $eventModel = new Event($this->pdo);
            $villes = $eventModel->fetchVillesByRegion($regionId);
            echo json_encode($villes);
        }
    }

    /**
     * Affiche tous les événements.
     */
    public function afficherTousLesEvenements()
    {
        $eventModel = new Event($this->pdo);

        $events = $eventModel->fetchAll();
        $categories = $eventModel->fetchCategories();
        $tags = $eventModel->fetchTags();
        $regions = $eventModel->fetchRegions();

        View::render('back/organisateur/addEvent.twig', [
            'events' => $events,
            'categories' => $categories,
            'tags' => $tags,
            'regions' => $regions,
        ]);
    }

    /**
     * Supprime un événement.
     */
    public function deleteEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
            $eventId = (int)$_POST['event_id'];
            $eventModel = new Event($this->pdo);

            try {
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

    /**
     * Affiche le formulaire d'édition d'un événement.
     */
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

    /**
     * Met à jour un événement.
     */

     public function updateEvent($eventId)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $eventModel = new Event($this->pdo);

        // Récupérer les sponsors
        $sponsors = [];
        if (isset($_POST['sponsors']) && is_array($_POST['sponsors'])) {
            foreach ($_POST['sponsors'] as $index => $sponsorData) {
                // Vérifier si le sponsor est marqué pour suppression
                if (isset($sponsorData['delete']) && $sponsorData['delete'] === "1") {
                    // Vérifier que sponsor_id est défini
                    if (isset($sponsorData['sponsor_id']) && is_numeric($sponsorData['sponsor_id'])) {
                        // Supprimer le sponsor de la base de données
                        $eventModel->removeSponsorFromEvent($eventId, (int)$sponsorData['sponsor_id']);
                    }
                    continue;
                }

                $sponsorName = $sponsorData['name'] ?? null;
                $sponsorImage = null;

                // Gérer l'upload de l'image du sponsor
                if (isset($_FILES['sponsors']['tmp_name'][$index]['image'])) {
                    $target_dir = __DIR__ . "/../../../public/assets/images/";
                    $target_file = $target_dir . basename($_FILES["sponsors"]["name"][$index]["image"]);
                    if (move_uploaded_file($_FILES["sponsors"]["tmp_name"][$index]["image"], $target_file)) {
                        $sponsorImage = basename($_FILES["sponsors"]["name"][$index]["image"]);
                    }
                }

                if ($sponsorName) {
                    $sponsors[] = [
                        'name' => $sponsorName,
                        'image' => $sponsorImage,
                    ];
                }
            }
        }

        // Données à mettre à jour
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
            'sponsors' => $sponsors, // Ajouter les sponsors
            'startEventAt' => $_POST['startEventAt'],
            'endEventAt' => $_POST['endEventAt'],
            'image' => $_FILES['event_image']['name'] ?? null,
            'ville_id' => (int)$_POST['ville_id'],
        ];

        // Gérer l'upload de l'image de l'événement
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/../../../public/assets/images/";
            $uploadFile = $uploadDir . basename($_FILES['event_image']['name']);

            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadFile)) {
                $data['image'] = basename($_FILES['event_image']['name']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload de l\'image.']);
                return;
            }
        }

        // Mettre à jour l'événement
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

    /**
     * Affiche les événements acceptés sur la page d'accueil.
     */
    public function displayEventsAcceptedHome()
    {
        $eventsHomePage = new Event($this->pdo);
        $eventsAccepted = $eventsHomePage->displayEventsAccepted();
        $categoryHomePage = $eventsHomePage->fetchCategories();
        $SponsorsHomePage = $eventsHomePage->fetchAllSponsors();

        View::render('front/home.twig', [
            'eventsAccepted' => $eventsAccepted,
            'categoryHomePage' => $categoryHomePage,
            'SponsorsHomePage' => $SponsorsHomePage,
        ]);
    }
}