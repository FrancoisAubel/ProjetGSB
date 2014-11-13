<?php
/** 
 * Script de contr�le et d'affichage du cas d'utilisation "Consulter une fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connect�
  if ( ! estVisiteurConnecte() ) {
      header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
  // acquisition des donn�es entr�es, ici le num�ro de mois et l'�tape du traitement
  $moisSaisi=lireDonneePost("lstMois", "");
  $etape=lireDonneePost("etape",""); 

  if ($etape != "demanderConsult" && $etape != "validerConsult") {
      // si autre valeur, on consid�re que c'est le d�but du traitement
      $etape = "demanderConsult";        
  } 
  if ($etape == "validerConsult") { // l'utilisateur valide ses nouvelles donn�es
                
      // v�rification de l'existence de la fiche de frais pour le mois demand�
      $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, obtenirIdUserConnecte());
      // si elle n'existe pas, on la cr�e avec les �lets frais forfaitis�s � 0
      if ( !$existeFicheFrais ) {
          ajouterErreur($tabErreurs, "Le mois demand� est invalide");
      }
      else {
          // r�cup�ration des donn�es sur la fiche de frais demand�e
          $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, obtenirIdUserConnecte());
      }
  }                                  
?>
  <!-- Division principale -->
  <div id="contenu">
      <h2>Mes fiches de frais</h2>
      <h3>Mois � s�lectionner : </h3>
      <form action="" method="post">
      <div class="corpsForm">
          <input type="hidden" name="etape" value="validerConsult" />
      <p>
        <label for="lstMois">Mois : </label>
        <select id="lstMois" name="lstMois" title="S�lectionnez le mois souhait� pour la fiche de frais">
            <?php
                // on propose tous les mois pour lesquels le visiteur a une fiche de frais
                $req = obtenirReqMoisFicheFrais(obtenirIdUserConnecte());
                $idJeuMois = mysql_query($req, $idConnexion);
                $lgMois = mysql_fetch_assoc($idJeuMois);
                while ( is_array($lgMois) ) {
                    $mois = $lgMois["mois"];
                    $noMois = intval(substr($mois, 4, 2));
                    $annee = intval(substr($mois, 0, 4));
            ?>    
            <option value="<?php echo $mois; ?>"<?php if ($moisSaisi == $mois) { ?> selected="selected"<?php } ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
            <?php
                    $lgMois = mysql_fetch_assoc($idJeuMois);        
                }
                mysql_free_result($idJeuMois);
            ?>
        </select>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input id="ok" type="submit" value="Valider" size="20"
               title="Demandez � consulter cette fiche de frais" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
      </p> 
      </div>
        
      </form>
<?php      

// demande et affichage des diff�rents �l�ments (forfaitis�s et non forfaitis�s)
// de la fiche de frais demand�e, uniquement si pas d'erreur d�tect� au contr�le
    if ( $etape == "validerConsult" ) {
        if ( nbErreurs($tabErreurs) > 0 ) {
            echo toStringErreurs($tabErreurs) ;
        }
        else {
?>
    <h3>Fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi,4,2))) . " " . substr($moisSaisi,0,4); ?> : 
    <em><?php echo $tabFicheFrais["libelleEtat"]; ?> </em>
    depuis le <em><?php echo $tabFicheFrais["dateModif"]; ?></em></h3>
    <div class="encadre">
    <p>Montant valid� : <?php echo $tabFicheFrais["montantValide"] ;
        ?>              
    </p>
<?php          
            // demande de la requ�te pour obtenir la liste des �l�ments 
            // forfaitis�s du visiteur connect� pour le mois demand�
            $req = obtenirReqEltsForfaitFicheFrais($moisSaisi, obtenirIdUserConnecte());
            $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
            echo mysql_error($idConnexion);
            $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            // parcours des frais forfaitis�s du visiteur connect�
            // le stockage interm�diaire dans un tableau est n�cessaire
            // car chacune des lignes du jeu d'enregistrements doit �tre doit �tre
            // affich�e au sein d'une colonne du tableau HTML
            $tabEltsFraisForfait = array();
            while ( is_array($lgEltForfait) ) {
                $tabEltsFraisForfait[$lgEltForfait["libelle"]] = $lgEltForfait["quantite"];
                $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            }
            mysql_free_result($idJeuEltsFraisForfait);
            ?>
  	<table class="listeLegere">
  	   <caption>Quantit�s des �l�ments forfaitis�s</caption>
        <tr>
            <?php
            // premier parcours du tableau des frais forfaitis�s du visiteur connect�
            // pour afficher la ligne des libell�s des frais forfaitis�s
            foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
            ?>
                <th><?php echo $unLibelle ; ?></th>
            <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            // second parcours du tableau des frais forfaitis�s du visiteur connect�
            // pour afficher la ligne des quantit�s des frais forfaitis�s
            foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
            ?>
                <td class="qteForfait"><?php echo $uneQuantite ; ?></td>
            <?php
            }
            ?>
        </tr>
    </table>
  	<table class="listeLegere">
  	   <caption>Descriptif des �l�ments hors forfait - <?php echo $tabFicheFrais["nbJustificatifs"]; ?> justificatifs re�us -
       </caption>
             <tr>
				<th class="montant">Utilisateur</th>   
                <th class="date">Date</th>
                <th class="libelle">Libell�</th>
                <th class="montant">Montant</th>
				<th class="montant"></th>                                
             </tr>
<?php          
            // demande de la requ�te pour obtenir la liste des �l�ments hors
            // forfait du visiteur connect� pour le mois demand�
            $req = obtenirReqEltsHorsForfaitFicheFraisemploye($moisSaisi);
            $idJeuEltsHorsForfait = mysql_query($req, $idConnexion);
            $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
			
			

            // ROBIN ta derni�re modif est ici. Ajout d'une fonction, d'une deuxi�me requete dans le while
            // parcours des �l�ments hors forfait 
            while ( is_array($lgEltHorsForfait) ) {
            ?>
                <tr>
				    <?php $reqdeux = obtenirnom($lgEltHorsForfait["idVisiteur"]);
					$idJeuEltsHorsForfaitdeux = mysql_query($reqdeux, $idConnexion);
					$lgEltHorsForfaitdeux = mysql_fetch_assoc($idJeuEltsHorsForfaitdeux);
					?>
				
				   <td><?php echo $lgEltHorsForfaitdeux["nom"] ; ?></td>
                   <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                   <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                   <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
				   
				   
                </tr>
            <?php
                $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);

            }
            mysql_free_result($idJeuEltsHorsForfait);

  ?>

    </table>
  </div>
<?php
        }
    }
?>    
  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 