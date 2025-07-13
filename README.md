# Eventbrite

## Structure de projet

```
app/
├── config/
│   ├── Database.php
│   └── database.sql
├── controllers/
│   ├── Authentication/
│   │   ├── AuthController.php
│   ├── backOffice/
│   │   ├── AdminController.php
│   │   ├── OrganizerController.php
│   │   ├── ParticipantController.php
│   │   ├── UserController.php
│   └── FrontOffice/
│       ├── blogController.php
│       ├── contactController.php
│       ├── EventController.php
│       └── HomeController.php
├── core/
│   ├── Auth.php
│   ├── Controller.php
│   ├── Router.php
│   ├── Security.php
│   ├── Session.php
│   └── Validator.php
├── views/
│   ├── Authentication/
│   │   ├── login.twig
│   │   └── register.twig
│   ├── back/
│   │   ├── Admin/
│   │   │   ├── dashboard.twig
│   │   │   ├── Event.twig
│   │   │   └── users.twig
│   ├── organiser/
│   │   ├── addEvent.twig
│   │   ├── categories.twig
│   │   ├── Event.twig
│   │   └── statistics.twig
│   ├── participant/
│   │   ├── myEvents.twig
│   └── Profile/
│       ├── Profile.twig
│       └── update.twig
├── public/
│   ├── assets/
│   ├── component/
│   ├── footer.php
│   ├── navbar.php
│   ├── .htaccess
│   ├── index.php
│   └── composer.json
├── vendor/
└── gitignore
```
