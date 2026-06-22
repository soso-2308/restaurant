<?php $active_page = 'dashboard'; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="icon"><i class="fas fa-calendar-check"></i></div>
        <div class="number"><?php echo $stats['reservations_mois']; ?></div>
        <div class="label">Réservations ce mois</div>
        <small style="color: <?php echo $stats['croissance'] >= 0 ? '#28a745' : '#dc3545'; ?>;">
            <?php echo $stats['croissance'] >= 0 ? '↑' : '↓'; ?> 
            <?php echo abs($stats['croissance']); ?>% vs mois dernier
        </small>
    </div>
    <div class="stat-card">
        <div class="icon"><i class="fas fa-users"></i></div>
        <div class="number"><?php echo $stats['couverts_mois']; ?></div>
        <div class="label">Couverts servis</div>
    </div>
    <div class="stat-card">
        <div class="icon"><i class="fas fa-star"></i></div>
        <div class="number"><?php echo $stats['note_moyenne']; ?>/5</div>
        <div class="label">Note moyenne (<?php echo $stats['total_avis']; ?> avis)</div>
    </div>
    <div class="stat-card">
        <div class="icon"><i class="fas fa-chair"></i></div>
        <div class="number"><?php echo $stats['taux_occupation']; ?>%</div>
        <div class="label">Taux d'occupation aujourd'hui</div>
    </div>
</div>

<div class="table-container" style="margin-bottom: 30px;">
    <h2><i class="fas fa-clock"></i> Réservations du jour (<?php echo date('d/m/Y'); ?>)</h2>
    
    <?php if (empty($reservations_today)): ?>
        <p style="color: #999; text-align: center; padding: 20px;">Aucune réservation pour aujourd'hui</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Heure</th>
                    <th>Client</th>
                    <th>Téléphone</th>
                    <th>Personnes</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations_today as $res): ?>
                    <tr>
                        <td><?php echo substr($res->getCreneau()->getHeureDebut(), 0, 5); ?></td>
                        <td><?php echo htmlspecialchars($res->getClient()->getNom()); ?></td>
                        <td><?php echo htmlspecialchars($res->getClient()->getTelephone()); ?></td>
                        <td><?php echo $res->getNombrePersonnes(); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $res->getStatut(); ?>">
                                <?php 
                                $statuts = ['confirmee' => '✅ Confirmée', 'annulee' => '❌ Annulée', 'terminee' => '✔️ Terminée'];
                                echo $statuts[$res->getStatut()] ?? $res->getStatut();
                                ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Évolution des réservations (graphique simple) -->
<div class="table-container">
    <h2><i class="fas fa-chart-line"></i> Évolution des réservations (7 jours)</h2>
    <div style="display: flex; gap: 10px; align-items: flex-end; height: 150px; padding-top: 20px;">
        <?php foreach ($evolution['values'] as $i => $value): ?>
            <div style="flex: 1; text-align: center;">
                <div style="background: #e8a87c; height: <?php echo max(5, $value * 15); ?>px; border-radius: 4px 4px 0 0; transition: height 0.5s;"></div>
                <span style="font-size: 12px; color: #666;"><?php echo $evolution['labels'][$i]; ?></span>
                <br><small><?php echo $value; ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</div>