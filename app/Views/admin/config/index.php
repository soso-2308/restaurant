<?php $active_page = 'config'; ?>

<h2 style="font-size: 18px; margin-bottom: 20px;">
    <i class="fas fa-cog"></i> Configuration du restaurant
</h2>

<form method="POST" action="/restaurant-ryoha/admin/config" 
      style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- Capacité -->
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Capacité de la salle</label>
            <input type="number" name="salle_capacite" 
                   value="<?php echo $configs['salle_capacite'] ?? 50; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
            <small style="color: #999;">Nombre maximum de couverts</small>
        </div>
        
        <!-- Délai d'annulation -->
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Délai d'annulation (heures)</label>
            <input type="number" name="delai_annulation" 
                   value="<?php echo $configs['delai_annulation'] ?? 2; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
            <small style="color: #999;">Annulation gratuite jusqu'à X heures avant</small>
        </div>
        
        <!-- Email notification -->
        <div style="grid-column: 1/-1;">
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Email de notification</label>
            <input type="email" name="email_notification" 
                   value="<?php echo $configs['email_notification'] ?? 'admin@ryoha.com'; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
            <small style="color: #999;">Email où seront envoyées les notifications de réservation</small>
        </div>
        
        <!-- Horaires Midi -->
        <div style="grid-column: 1/-1; border-top: 1px solid #eee; padding-top: 20px; margin-top: 10px;">
            <h3 style="margin-bottom: 15px;"><i class="fas fa-sun"></i> Service du Midi</h3>
        </div>
        
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Heure d'ouverture</label>
            <input type="time" name="heure_ouverture_midi" 
                   value="<?php echo $horaires['midi']['ouverture']; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Heure de fermeture</label>
            <input type="time" name="heure_fermeture_midi" 
                   value="<?php echo $horaires['midi']['fermeture']; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        
        <!-- Horaires Soir -->
        <div style="grid-column: 1/-1; border-top: 1px solid #eee; padding-top: 20px; margin-top: 10px;">
            <h3 style="margin-bottom: 15px;"><i class="fas fa-moon"></i> Service du Soir</h3>
        </div>
        
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Heure d'ouverture</label>
            <input type="time" name="heure_ouverture_soir" 
                   value="<?php echo $horaires['soir']['ouverture']; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        
        <div>
            <label style="font-weight: 600; display: block; margin-bottom: 5px;">Heure de fermeture</label>
            <input type="time" name="heure_fermeture_soir" 
                   value="<?php echo $horaires['soir']['fermeture']; ?>" 
                   style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 8px;">
        </div>
        
        <!-- Jours de fermeture -->
        <div style="grid-column: 1/-1; border-top: 1px solid #eee; padding-top: 20px; margin-top: 10px;">
            <h3 style="margin-bottom: 15px;"><i class="fas fa-calendar-times"></i> Jours de fermeture</h3>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php 
                $joursFermeture = isset($configs['jours_fermeture']) ? json_decode($configs['jours_fermeture'], true) : ['Dimanche'];
                if (!is_array($joursFermeture)) $joursFermeture = ['Dimanche'];
                
                $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                foreach ($jours as $jour): 
                ?>
                    <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                        <input type="checkbox" name="jours_fermeture[]" value="<?php echo $jour; ?>" 
                               <?php echo in_array($jour, $joursFermeture) ? 'checked' : ''; ?>>
                        <?php echo $jour; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 30px;">
        <button type="submit" name="submit" 
                style="background: #e8a87c; color: #1a1a1a; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
            <i class="fas fa-save"></i> Enregistrer la configuration
        </button>
        <a href="/restaurant-ryoha/admin" 
           style="margin-left: 10px; background: #6c757d; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</form>