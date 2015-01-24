<?php include('/admin/ConnexionBD.php'); ?>

<?php

	function valider(){
  	if(isset($_POST['generer'])){
    	generer_graph();
  	}
  }

//fonction qui génére un graph en fonction des capteurs et des dates selectionnées
	function generer_graph()
  {
		if(isset($_POST['capteurs'])&& isset($_POST['dateDebut']) && isset($_POST['dateFin'])){
			//echo "Interval du :";
			//echo $_POST['dateDebut'];
			//echo " au ";
			//echo $_POST['dateFin'];
			$dateDebut = $_POST['dateDebut'];
			$dateFin = $_POST['dateFin'];
			$donnees = [];
			foreach($_POST['capteurs'] as $box) {
				$capteur = getCapteur($box);
				$dataCapteur = getData($capteur, $dateDebut, $dateFin);
				//testPrint($dataCapteur); test pour afficher les valeurs du capteurs
				array_push($donnees, $dataCapteur);
			}
			//printDonnees($donnees);
			//echo $donnees[0];
			//echo $donnees[1];
			return $donnees;

		}
		else{
			echo "choisissez un capteur, et des dates";
		}
	}

//fonction qui prend en paramètre le nom d'un capteur et renvoie le capteur
	function getCapteur($nom){
		global $connexion;
		$pstmt = $connexion->prepare("Select * from capteur where nomC = :nom");
    	$pstmt -> bindParam(':nom', $nom);
    	$pstmt -> execute();
   	 	$capteur = $pstmt -> fetch();
    	return $capteur;
	}

//fonction qui prend en paramètre un capteur, une date de début et une date de fin et retourne les données enregistrer par le capteur pendant cet interval de temps
	function getData($capteur , $dateDebut, $dateFin){
		global $connexion;

		//transformation des dates pour qu'elles soient reconnu par MySQL
		$date_explosee = explode("/", $dateDebut);  // la fonction explode permet de séparer la chaine en tableau selon un délimiteur

  	$jourDeb = $date_explosee[1];
 		$moisDeb = $date_explosee[0];
 		$anneeDeb = $date_explosee[2];

		$date_explosee = explode("/", $dateFin);

 	 	$jourFin = $date_explosee[1];
 	 	$moisFin = $date_explosee[0];
 	 	$anneeFin = $date_explosee[2];

	 	$dateDebut = $anneeDeb."-".$jourDeb."-".$moisDeb;
 		$dateFin = $anneeFin."-".$jourFin."-".$moisFin;

 		//requete de selection des données
		$stmt = $connexion->prepare("Select date,valeur from donnees where date >= :date_d and date <= :date_f and idC = :id_capteur ");
		//'2014-01-15 19:00:13'
    $stmt -> bindParam(':date_d' , $dateDebut);
    $stmt -> bindParam(':date_f' , $dateFin);
    $stmt -> bindParam(':id_capteur' , $capteur[0]);
    $stmt -> execute();

    $i = 0;
    $data = array();
    //$date = array(); non prise en compte de la date des données pour le moment

    foreach($stmt as $requete) {
    	$donneeX = array();
    	//test
    	//
    	$date_explosee = explode("-", $requete[0]);
  		$jourDeb = $date_explosee[2];
 			$moisDeb = $date_explosee[1]-1;
 			$anneeDeb = $date_explosee[0];

 			$jourHeure = explode(" ", $jourDeb);
 			$jour = $jourHeure[0];
 			//test
    	//

 			$temp = explode(":", $jourHeure[1]);
 			$heure = $temp[0];
 			$minute = $temp[1];
 			$seconde = $temp[2];


    	$donnee = "[Date.UTC(".$anneeDeb.", ".$moisDeb.", ".$jour.", ".$heure.", ".$minute.", ".$seconde."), ".$requete[1]." ]";
      //$donnee += ", ".$requete[1]." ]";
      $data[$i] = $donnee;
      $i = $i+1;
    }
    return $data;
	}

	function testPrint($data){
		echo "<br> Données : ";
		foreach ($data as $d) {
			echo " / ";
			echo $d;
		}
	}

	function printDonnees($donnees){
		echo "<br> Liste donnes :";
		foreach ($donnees as $data) {
			testPrint($data);
		}
	}




?>



