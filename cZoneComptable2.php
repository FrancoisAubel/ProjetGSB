<?php
if(isset($_POST['liste1'])){
	//si la liste a été "postée" c'est à dire choix fait
	$liste1=$_POST['liste1'];
}else{
	$liste1=-1;
}
?>
<TABLE border=0 width=97% BGCOLOR=#FFFFFF>
<TR align="center">
<TD> Choix d'un utilisateur</TD>
</TR>
</TABLE>
<BR><BR>
Sélectionnez un utilisateur :
<form name="form1" method="post" action="">
<select name="liste1" onchange=" form1.submit();">
<option value=-1>-- Choisissez -- </option> <!-- il faut cette ligne pour avoir obliagtoirement un changement -->
<?php

$connection = mysql_connect('localhost', 'root', 'root');
$base = mysql_select_db('gsb_frais');

$requete = "SELECT nom FROM visiteur";
$execution_requete = mysql_query($requete);
while($total = mysql_fetch_array($execution_requete))

//Liste déroulante
{
echo "<option value=\"".$total["nom"]."\"";
if($liste1==$total['nom']) { echo "selected"; }//ça c'est pour garder la selection lors du réaffichage
echo ">".$total['nom']."</option>\n";

}

?>
</select>
</form>
<?php
if($liste1 != -1){ //si on a fait un choix
//on refait une requette avec une condition
$requete = "SELECT nom, prenom, cp, adresse, ville FROM visiteur WHERE nom='".$liste1."'";
$execution_requete = mysql_query($requete);

// on affiche les valeurs correspondantes au nom selectionné, pas besoin de boucle while, on ne récupère qu'un seul enregistrement
$total = mysql_fetch_array($execution_requete);	
//echo "Nom: ".$total['nom']."<br />Matricule: ".$total['matricule']."<br />Vacation: ".$total['vacation']."<br />Equipe: ".$total['equipe'];
// }  si on déplace cette accolade plus bas ça a l'avantage de na pas afficher la partie Informations tant que le choix n'a pas été fait dans la lsite1
?>

<br />
</div>
<div id="right_bas"></div>
<div id="right_haut"></div>
<div id="rightbk">
<br />

<TABLE border='0' width='97%' bgcolor='#FFFFFF'>
<TR align='center'>
<TD> Informations</TD>
</TR>
</TABLE>

<form method="post" action="">
<TABLE BORDER="0">
<CAPTION> </CAPTION>
<TR>
<TH>Nom :</TH>
<TD><input type="text" name="nom" value="<?php echo $total['nom'] ?>" size="20" ></TD>
<TH></TH>
<TH></TH>
<TH>Prénom :</TH>
<TD><input type="text" name="prenom" value="<?php echo $total['prenom'] ?>" size="20" ></TD>
<TR></TR>
<TH>Adresse :</TH>
<TD><input type="text" name="adresse" value="<?php echo $total['adresse'] ?>" size="35" ></TD>
<TH></TH>
<TH></TH>
<TH>Code Postal : </TH>
<TD><input type="text" name="cp" value="<?php echo $total['cp'] ?>" size="20" ></TD>
<TH>Ville : </TH>
<TD><input type="text" name="ville" value="<?php echo $total['ville'] ?>" size="20" ></TD>
</TR>
</TABLE>
<br><br>

<!-- <input type="hidden" name="id" value="<?php echo $total['id'] ?>">-->
<input type="submit" name="supprimer" value="Supprimer">
</form>
<?php
} // accolade de fin de if liste postee


// si je comprend bien tu veux supprimer l'agent qui est affiché
//donc dans ce cas mets simplement un champ caché contenant l'id (voir plus haut)

if(isset($_POST['supprimer'])){
	//on ne fait ça que si bouton supprimer cliké
	$id = $_POST["id"];
	$result = mysql_query("DELETE FROM visiteur WHERE id='".$id."' LIMIT 1");
	//on met limite 1 pour plus de sécurité si pb on supprime pas tout 

	if (!$result) {
		echo "La suppression a échouée<br>";
	} else {
		echo "Agent supprimé !<br>";
	}
}
?>