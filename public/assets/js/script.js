
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
        var formData = new FormData(this);
        formData.append("action", "insert");

        $.ajax({
            url: "/../../../app/controllers/frontOffice/EventController.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    window.location.href = result.redirect_url;
                } else {
                    console.error(result.error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Erreur lors de l'envoi du formulaire :", error);
            }
        });
    });

    // Charger les catégories et sponsors via AJAX
    $.ajax({
        url: "/../../../app/controllers/frontOffice/EventController.php",
        type: "POST",
        data: { action: "fetchFormData" },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.categories && result.sponsors) {
                // Remplir les options des catégories
                let categoryOptions = '<option value="" disabled selected>Choisir une catégorie</option>';
                result.categories.forEach(category => {
                    categoryOptions += `<option value="${category.category_id}">${category.name}</option>`;
                });
                $("select[name='category_id']").html(categoryOptions);

                // Remplir les options des sponsors
                let sponsorOptions = '<option value="" selected>Aucun sponsor</option>';
                result.sponsors.forEach(sponsor => {
                    sponsorOptions += `<option value="${sponsor.sponsor_id}">${sponsor.name}</option>`;
                });
                $("select[name='sponsor_id']").html(sponsorOptions);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors du chargement des données :", error);
        }
    });
});

    // <script src="../../../../public/assets/js/script.js"></script> 
