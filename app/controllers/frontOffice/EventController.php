<?php
namespace App\controllers\frontOffice;

use App\Models\Event;
use App\Config\Database;
use PDO;

class EventController {
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstanse()->getConnection();
    }

    public function createEvent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == "insert") {
            $event = new Event($this->pdo);
            
            $event->setTitle($_POST['title']);
            $event->setDescription($_POST['description']);
            $event->setEventMode($_POST['eventMode']);
            $event->setCapacite((int) $_POST['capacite']);
            $event->setSituation($_POST['situation']);
            $event->setStartEventAt(new \DateTime($_POST['startEventAt']));
            $event->setEndEventAt(new \DateTime($_POST['endEventAt']));
            
            if ($_POST['eventMode'] == "presentiel") {
                $event->setAdresse($_POST['adresse']);
                $event->setLienEvent(null);
            } else {
                $event->setLienEvent($_POST['lienEvent']);
                $event->setAdresse(null);
            }

            if ($_POST['priceType'] == "payant") {
                $event->setPrice((float)$_POST['price']);
            } else {
                $event->setPrice(null);
            }

            $event->setCategoryId((int) $_POST['category_id']);
            $event->setSponsorId($_POST['sponsor_id'] ? (int) $_POST['sponsor_id'] : null);

            $eventId = $event->insert();
            echo json_encode(["success" => true, "event_id" => $eventId]);
        }
    }

}
