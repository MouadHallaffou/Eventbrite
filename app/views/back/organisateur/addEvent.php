<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4 text-center">Créer un Nouvel Événement</h2>

        <form id="eventForm" action="#" method="POST" class="space-y-4">

            <!-- Titre -->
            <div>
                <label class="block font-semibold">Titre de l'événement</label>
                <input type="text" name="title" class="w-full p-2 border rounded-lg" placeholder="Entrez le titre" required>
            </div>

            <!-- Description -->
            <div>
                <label class="block font-semibold">Description</label>
                <textarea name="description" class="w-full p-2 border rounded-lg" placeholder="Décrivez l'événement" required></textarea>
            </div>

            <!-- Mode de l'événement -->
            <div>
                <label class="block font-semibold">Mode de l'événement</label>
                <div class="flex space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="eventMode" value="presentiel" class="event-mode" required>
                        <span>Présentiel</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="eventMode" value="en ligne" class="event-mode">
                        <span>En ligne</span>
                    </label>
                </div>
            </div>

            <!-- Adresse -->
            <div id="adresseField" class="hidden">
                <label class="block font-semibold">Adresse</label>
                <input type="text" name="adresse" class="w-full p-2 border rounded-lg" placeholder="Entrez l'adresse">
            </div>

            <!-- Lien URL -->
            <div id="lienEventField" class="hidden">
                <label class="block font-semibold">Lien de l'événement</label>
                <input type="url" name="lienEvent" class="w-full p-2 border rounded-lg" placeholder="Ex: https://zoom.com/event">
            </div>

            <!-- Type d'événement -->
            <div>
                <label class="block font-semibold">Type d'événement</label>
                <div class="flex space-x-4">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="isPaid" value="gratuit" class="payment-type" required>
                        <span>Gratuit</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="isPaid" value="payant" class="payment-type">
                        <span>Payant</span>
                    </label>
                </div>
            </div>

            <!-- Prix -->
            <div id="priceField" class="hidden">
                <label class="block font-semibold">Prix (en €)</label>
                <input type="number" name="price" class="w-full p-2 border rounded-lg" min="0" step="0.01" placeholder="Entrez le prix">
            </div>

            <!-- Capacité -->
            <div>
                <label class="block font-semibold">Capacité</label>
                <input type="number" name="capacite" class="w-full p-2 border rounded-lg" min="1" placeholder="Nombre maximum de participants" required>
            </div>

            <!-- Catégorie -->
            <div>
                <label class="block font-semibold">Catégorie</label>
                <select name="category_id" class="w-full p-2 border rounded-lg" required>
                    <option value="" disabled selected>Choisir une catégorie</option>
                    {% for category in categories %}
                    <option value="{{ category.id }}">{{ category.name }}</option>
                    {% endfor %}
                </select>
            </div>

            <!-- Sponsor (Optionnel) -->
            <div>
                <label class="block font-semibold">Sponsor (Optionnel)</label>
                <select name="sponsor_id" class="w-full p-2 border rounded-lg">
                    <option value="" selected>Aucun sponsor</option>
                    {% for sponsor in sponsors %}
                    <option value="{{ sponsor.id }}">{{ sponsor.name }}</option>
                    {% endfor %}
                </select>
            </div>

            <!-- Dates -->
            <div>
                <label class="block font-semibold">Date de début</label>
                <input type="date" name="startEventAt" class="w-full p-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block font-semibold">Date de fin</label>
                <input type="date" name="endEventAt" class="w-full p-2 border rounded-lg" required>
            </div>

            <!-- User ID (Organisateur) -->
            <input type="hidden" name="user_id" value="1">

            <!-- Bouton Soumettre -->
            <div class="text-center">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Créer l'Événement
                </button>
            </div>

        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Gestion du mode d'événement (présentiel / en ligne)
            $(".event-mode").change(function() {
                let selectedMode = $("input[name='eventMode']:checked").val();
                if (selectedMode === "presentiel") {
                    $("#adresseField").removeClass("hidden");
                    $("#lienEventField").addClass("hidden");
                } else {
                    $("#adresseField").addClass("hidden");
                    $("#lienEventField").removeClass("hidden");
                }
            });

            // Gestion du type de paiement (gratuit / payant)
            $(".payment-type").change(function() {
                let selectedPayment = $("input[name='isPaid']:checked").val();
                if (selectedPayment === "payant") {
                    $("#priceField").removeClass("hidden");
                } else {
                    $("#priceField").addClass("hidden");
                }
            });

            // Soumission du formulaire via AJAX
            $("#eventForm").submit(function(e) {
                e.preventDefault();
                var eventData = $(this).serialize() + "&action=insert";

                $.ajax({
                    url: "controllers/frontOffice/EventController.php",
                    type: "POST",
                    data: eventData,
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors de l'envoi du formulaire :", error);
                    }
                });
            });
        });
    </script>

</body>

</html>