<?php $active_page = 'reservations'; ?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
    <h2 style="font-size: 18px;">
        <i class="fas fa-calendar-alt"></i> Gestion des réservations
    </h2>
    
    <form method="GET" style="display: flex; gap: 10px; align-items: center;">
        <label style="font-weight: 500;">Date :</label>
        <input type="date" name="date" value="<?php echo $date_selected; ?>" 
               style="padding: 8px 12px; border: 2px solid #ddd; border-radius: 8px;">
        <button type="submit" style="padding: 8px 20px; background: #e8a87c; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-search"></i> Voir
        </button>
    </form>
</div>

<div class="table-container">
    <?php if (empty($reservations)): ?>
        <p style="color: #999; text-align: center; padding: 40px 0;">
            <i class="fas fa-calendar-times" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
            Aucune réservation pour le <?php echo date('d/m/Y', strtotime($date_selected)); ?>
        </p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Heure</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Personnes</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr id="reservation-<?php echo $res->getId(); ?>">
                        <td>
                            <strong><?php echo substr($res->getCreneau()->getHeureDebut(), 0, 5); ?></strong>
                            <br><small style="color: #999;"><?php echo substr($res->getCreneau()->getHeureFin(), 0, 5); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($res->getClient()->getNom()); ?></td>
                        <td><?php echo htmlspecialchars($res->getClient()->getTelephone()); ?></td>
                        <td><?php echo htmlspecialchars($res->getClient()->getEmail() ?? '-'); ?></td>
                        <td><?php echo $res->getNombrePersonnes(); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $res->getStatut(); ?>">
                                <?php 
                                $statuts = [
                                    'confirmee' => '✅ Confirmée',
                                    'annulee' => '❌ Annulée',
                                    'terminee' => '✔️ Terminée'
                                ];
                                echo $statuts[$res->getStatut()] ?? $res->getStatut();
                                ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($res->getStatut() === 'confirmee'): ?>
                                <select onchange="changerStatut(<?php echo $res->getId(); ?>, this.value)" 
                                        style="padding: 4px 8px; border-radius: 4px; border: 1px solid #ddd;">
                                    <option value="confirmee" selected>✅ Confirmée</option>
                                    <option value="terminee">✔️ Terminée</option>
                                    <option value="annulee">❌ Annuler</option>
                                </select>
                            <?php elseif ($res->getStatut() === 'terminee'): ?>
                                <span style="color: #999;">Terminée</span>
                            <?php else: ?>
                                <span style="color: #999;">Annulée</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function changerStatut(id, statut) {
    if (statut === 'annulee') {
        if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            return;
        }
    }
    
    $.ajax({
        url: '/restaurant-ryoha/admin/reservations/changer-statut',
        method: 'POST',
        data: { id: id, statut: statut },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('✅ ' + response.message);
                location.reload();
            } else {
                alert('❌ ' + response.message);
            }
        },
        error: function() {
            alert('Erreur lors de la mise à jour');
        }
    });
}
</script>