<?php
/** 
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */
?>

    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil">
    <?php      
      if (estVisiteurConnecte()) {
			
		$idUser = obtenirIdUserConnecte() ;
		$lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
		$requeteNiveau = "SELECT Niveau FROM visiteur WHERE id='" . $idUser . "'";
		$req = mysql_query($requeteNiveau);
		$donnees = mysql_fetch_assoc($req);	
	    $niveauReq = $donnees['Niveau'];
		$nom = $lgUser['nom'];
		$prenom = $lgUser['prenom'];            
    ?>
        <h2>
    <?php  
            echo $nom . " " . $prenom ;
    ?>
        </h2>
        <h3>Visiteur médical</h3>        
    <?php
       }
    ?>  
      </div>  
<?php      
  if (estVisiteurConnecte() ) {
?>
        <ul id="menuList">
           <li class="smenu">
              <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
           </li>
           <li class="smenu">
              <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
           </li>
           <li class="smenu">
              <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
           </li>
           <li class="smenu">
              <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
           </li>
		   <?php 
		   $niveauSection = 1;
		   if ($niveauReq == $niveauSection){
		   ?>
			   <li class="smenu">
				  <a href="cFicheDeFraisEmployes.php" title="Consultation de mes fiches de frais">Fiches de Frais des employés</a>
			   </li>
			   <li class="smenu">
				  <a href="cZoneComptable.php" title="Zone Comptable">Zone Comptable</a>
			   </li>
			<?php
		   }
		   ?>
         </ul>
        <?php
          // affichage des éventuelles erreurs déjà détectées
          if ( nbErreurs($tabErreurs) > 0 ) {
              echo toStringErreurs($tabErreurs) ;
          }
  }
        ?>
    </div>
    