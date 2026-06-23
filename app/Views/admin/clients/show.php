<?php $active_page = 'clients'; ?>

<div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
    <h2>Détail du client</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
        <div><strong>Nom :</strong> <?php echo htmlspecialchars($client->getNom()); ?></div>
        <div><strong>Téléphone :</strong> <?php echo htmlspecialchars($client->getTelephone()); ?></div>
        <div><strong>Email :</strong> <?php echo htmlspecialchars($client->getEmail() ?? '-'); ?></div>
        <div><strong>Inscrit le :</strong> <?php echo date('d/m/Y H:i', strtotime($client->getCreatedAt())); ?></div>
    </div>
</div>

<h3>Historique des réservations (<?php echo count($reservations); ?>)</h3>
<div class="table-container">
    <?php if (empty($reservations)): ?>
        <p style="color: #999; text-align: center; padding: 20px;">Aucune réservation</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Personnes</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <?php $creneau = $res->creneau(); ?>
                    <tr>
                        <td>#<?php echo $res->getId(); ?></td>
                        <td><?php echo $creneau ? date('d/m/Y', strtotime($creneau->getDateReservation())) : ''; ?></td>
                        <td><?php echo $creneau ? substr($creneau->getHeureDebut(), 0, 5) : ''; ?></td>
                        <td><?php echo $res->getNombrePersonnes(); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $res->getStatut(); ?>">
                                <?php echo $res->getStatut(); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div style="margin-top: 20px;">
    <a href="<?php echo BASE_URL; ?>/admin/clients" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">Retour</a>
</div>