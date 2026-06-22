<?php $active_page = 'reservation'; ?>

<div class="container">
    <h1 class="page-title">Réserver une <span>table</span></h1>
    
    <div class="reservation-wrapper">
        <form id="reservationForm" class="reservation-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Date *</label>
                    <input type="date" id="date" name="date" 
                           min="<?php echo date('Y-m-d'); ?>" 
                           value="<?php echo $date; ?>" required>
                </div>
                <div class="form-group">
                    <label for="nb_personnes">Personnes *</label>
                    <input type="number" id="nb_personnes" name="nb_personnes" 
                           min="1" max="20" value="<?php echo $nb_personnes; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Créneaux disponibles</label>
                <div id="creneauxContainer" class="creneaux-grid">
                    <?php if (!empty($creneaux)): ?>
                        <?php foreach ($creneaux as $creneau): ?>
                            <div class="creneau-option" data-id="<?php echo $creneau->getId(); ?>">
                                <?php echo substr($creneau->getHeureDebut(), 0, 5); ?> - <?php echo substr($creneau->getHeureFin(), 0, 5); ?>
                                <small><?php echo $creneau->getCouvertsDisponibles(); ?> places</small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-creneaux">Aucun créneau disponible pour cette date</p>
                    <?php endif; ?>
                </div>
                <input type="hidden" id="creneau_id" name="creneau_id" value="">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone *</label>
                    <input type="tel" id="telephone" name="telephone" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email">
            </div>
            
            <div class="form-group">
                <label for="commentaire">Message (optionnel)</label>
                <textarea id="commentaire" name="commentaire" rows="3"></textarea>
            </div>
            
            <button type="submit" id="btnReserver" class="btn btn-primary btn-block">
                <i class="fas fa-check"></i> Confirmer la réservation
            </button>
        </form>
        
        <div id="resultat" class="resultat" style="display: none;"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Sélection des créneaux
    $('.creneau-option').click(function() {
        $('.creneau-option').removeClass('selected');
        $(this).addClass('selected');
        $('#creneau_id').val($(this).data('id'));
    });
    
    // Changement date/personnes -> recharge les créneaux
    $('#date, #nb_personnes').on('change', function() {
        chargerCreneaux();
    });
    
    function chargerCreneaux() {
        const date = $('#date').val();
        const nbPersonnes = $('#nb_personnes').val();
        if (!date) return;
        
        $.ajax({
            url: '/restaurant-ryoha/api/disponibilites',
            method: 'POST',
            data: { date: date, nb_personnes: nbPersonnes },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let html = '';
                    if (response.creneaux.length === 0) {
                        html = '<p class="no-creneaux">Aucun créneau disponible</p>';
                    } else {
                        response.creneaux.forEach(function(c) {
                            html += `
                                <div class="creneau-option" data-id="${c.id}">
                                    ${c.heure_debut.substring(0,5)} - ${c.heure_fin.substring(0,5)}
                                    <small>${c.couverts_disponibles} places</small>
                                </div>
                            `;
                        });
                    }
                    $('#creneauxContainer').html(html);
                    $('#creneau_id').val('');
                    
                    $('.creneau-option').click(function() {
                        $('.creneau-option').removeClass('selected');
                        $(this).addClass('selected');
                        $('#creneau_id').val($(this).data('id'));
                    });
                }
            },
            error: function() {
                alert('Erreur lors du chargement des créneaux');
            }
        });
    }
    
    // Soumission du formulaire
    $('#reservationForm').on('submit', function(e) {
        e.preventDefault();
        
        const creneauId = $('#creneau_id').val();
        if (!creneauId) {
            alert('Veuillez sélectionner un créneau.');
            return;
        }
        
        const formData = {
            nom: $('#nom').val(),
            telephone: $('#telephone').val(),
            email: $('#email').val(),
            creneau_id: creneauId,
            nb_personnes: $('#nb_personnes').val(),
            commentaire: $('#commentaire').val(),
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        $('#btnReserver').prop('disabled', true).text('Traitement...');
        
        $.ajax({
            url: '/restaurant-ryoha/api/reservation/confirmer',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#btnReserver').prop('disabled', false).text('Confirmer la réservation');
                if (response.success) {
                    $('#resultat').html(`
                        <div class="resultat-success">
                            <i class="fas fa-check-circle"></i>
                            <h3>Réservation confirmée !</h3>
                            <p>${response.message}</p>
                        </div>
                    `);
                    $('#reservationForm').hide();
                } else {
                    let msg = response.message || 'Une erreur est survenue.';
                    if (response.errors) {
                        msg = response.errors.join('<br>');
                    }
                    $('#resultat').html(`
                        <div class="resultat-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>${msg}</p>
                        </div>
                    `);
                }
                $('#resultat').show();
            },
            error: function() {
                $('#btnReserver').prop('disabled', false).text('Confirmer la réservation');
                $('#resultat').html(`
                    <div class="resultat-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Erreur de connexion au serveur.</p>
                    </div>
                `);
                $('#resultat').show();
            }
        });
    });
    
    // Charger les créneaux au chargement
    chargerCreneaux();
});
</script>