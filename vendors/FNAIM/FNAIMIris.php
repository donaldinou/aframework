<?php
// Set error reporting to ignore notices
vendor("XMLSerializer/Serializer");
set_time_limit(300);

// <--------------------->
$DICO_FNAIM = array();

$DICO_FNAIM["TYPE_MANDAT"] = 
	array(
		'S' => 'Simple',
		'E' => 'Exclusif',
		'P' => 'Privilégié',
	);
	
$DICO_FNAIM["ETAT_GENERAL"] = 
	array(
		68 => 'Habitable',
		69 => 'Travaux à prévoir',
		70 => 'Très bon état',
	);
	
$DICO_FNAIM["CUISINE"] = 
	array(
		18 => 'Aménagée',
		19 => 'Equipée',
		20 => 'Kitchenette',
		337 => 'Simple',
		359 => 'Sans cuisine',
	);
		
$DICO_FNAIM["CONSTRUCTION"] = 
	array(
		41 => 'Pierre',
		42 => 'Brique',
		43 => 'Parpaing',
		44 => 'Béton',
		45 => 'Bois',
		46 => 'Meulière',
		47 => 'Colombage',
	);
		
$DICO_FNAIM["MITOYENNETE"] = 
	array(
		54 => 'Indépendant',
		55 => '1 côté',
		56 => '2 côtés',
		57 => '3 côtés',
	);
		
$DICO_FNAIM["STANDING"] = 
	array(
		1 => 'Normal',
		2 => 'Bon',
		3 => 'Grand standing',
	);
		
$DICO_FNAIM["COUVERTURE"] = 
	array(
		48 => 'Terrasse',
		49 => 'Tuiles',
		50 => 'Ardoises',
		51 => 'Chaumes',
		52 => 'Autre',
		53 => 'Tôle',
	);
		
$DICO_FNAIM["EXPOSITION"] = 
	array(
		7 => 'Est',
		8 => 'Nord',
		9 => 'Nord-est',
		10 => 'Nord-ouest',
		11 => 'Ouest',
		12 => 'Sud',
		13 => 'Sud-ouest',
		14 => 'Sud-est',
	);
		
$DICO_FNAIM["CHAUFFAGE"] = 
	array(
		21 => 'Collectif',
		22 => 'Individuel',
		23 => 'Central',
		24 => 'Oui sans précision',
		25 => 'Pompe à chaleur',
		26 => 'Climatisation',
	);
		
$DICO_FNAIM["MECANISME_CHAUFFAGE"] = 
	array(
		27 => 'Radiateur',
		28 => 'Au sol',
		29 => 'Air pulsé',
		30 => 'Convecteurs',
	);
		
$DICO_FNAIM["MODE_CHAUFFAGE"] = 
	array(
		31 => 'Gaz',
		32 => 'Electrique',
		33 => 'Fuel',
		34 => 'Autres',
	);
			
$DICO_FNAIM["LOCALISATION"] = 
	array(
		4 => 'Centre ville',
		5 => 'Agglomération',
		6 => 'Hors agglomération',
	);
			
$DICO_FNAIM["EAU_CHAUDE"] = 
	array(
		89 => 'Ballon électrique',
		90 => 'Chaudière',
		91 => 'Collective',
		92 => 'Chauffage central',
		93 => 'Gaz',
		94 => 'Individel',
	);
				
$DICO_FNAIM["DESCRIPTIF"] = 
	array(
		120 => 'Seul tenant',
		121 => 'Morcelé',
	);
				
$DICO_FNAIM["ANCIENNETE"] = 
	array(
		97 => 'Neuf',
		98 => 'Récent',
		99 => 'Ancien',
		100 => 'Rénové',
	);
				
$DICO_FNAIM["ZONE_ECONOMIQUE"] = 
	array(
		110 => 'Industrielle',
		111 => 'Rurale',
		112 => 'Urbaine',
	);
				
$DICO_FNAIM["GROS_OEUVRE"] = 
	array(
		101 => 'Trés bon',
		102 => 'Bon',
		103 => 'Moyen',
	);
				
$DICO_FNAIM["COPROPRIETE"] = 
	array(
		221 => 'Copropriété',
		234 => 'Pleine propriété',
	);
				
$DICO_FNAIM["TYPE_TELEPHONE"] = 
	array(
		257 => 'Téléphone',
		258 => 'Téléphone individuel',
		258 => 'Téléphone collectif',
	);
	
$DICO_FNAIM["PROXIMITES"] = 
	array(
		240 => 'SNCF',
		241 => 'Métro / RER',
		242 => 'Bus / Gare Routière',
		243 => 'Aérogare',
		244 => 'Accès routier',
		245 => 'Accès tout tonnage',
		246 => 'Commerces',
		247 => 'Marché d\'Intérêt Régional',
		248 => 'Marché d\'Intérêt National',
		249 => 'Voie Fluviale',
	);

// NEGOCIATEUR
$DICO_FNAIM["CIVILITE"] = 
	array(
		1 => 'Monsieur',
		2 => 'Madame',
		3 => 'Mademoiselle',
	);

// -- VENTE
$DICO_FNAIM["REGIME_FISCAL"] = 
	array(
		37 => 'Droit d\'enregistrement',
		38 => 'TVA',
		39 => 'Cession de parts',
		40 => 'Transfert d\'actions',
	);

// -- LOCATION
$DICO_FNAIM["ETAT_LIEUX"] = 
	array(
		95 => 'Agence',
		96 => 'Huissier',
	);
	
// -- LOCATION
$DICO_FNAIM["OCCUPE_PAR"] = 
	array(
		35 => 'Locataire',
		36 => 'Propriétaire',
	);
	
$DICO_FNAIM["CATEGORIE"] = 
	array(
		// -- APPARTEMENT
		1 => 'Appartement',
		2 => 'Appartement rénové',
		3 => 'Habitation de loisirs',
		4 => 'Appartement ancien',
		5 => 'Appartement bourgeois',
		6 => 'Appartement neuf',
		7 => 'Appartement récent',
		8 => 'Appartement à rénover',
		9 => 'Demeure',
		// -- MAISON
		10 => 'Fermette',
		11 => 'Habitation de loisirs',
		12 => 'Hôtel particulier',
		13 => 'Maison individuelle',
		14 => 'Maison de campagne',
		15 => 'Maison neuve',
		16 => 'Pavillon',
		17 => 'Villa',
		18 => 'Mas',
		18 => 'Maison de village',
		20 => 'Maison',
		// -- DEMEURE
		21 => 'Château',
		22 => 'Demeure ancienne',
		23 => 'Demeure contemporaine',
		24 => 'Mas',
		25=> 'Demeure traditionnelle',
		26 => 'Manoir',
		27 => 'Maison de maîtres',
	);
	
// -- DEMEURE
$DICO_FNAIM["ETAT_INTERIEUR"] = 
	array(
		71 => 'Sompteux',
		72 => 'A refraîchir',
		73 => 'Trés bon',
		74 => 'Bon',
		75 => 'Etat moyen',
		76 => 'Habitable en l\'etat',
		77 => 'Travaux à prévoir',
		78 => 'A aménager',
		79 => 'A réhabiliter',
		80 => 'A rénover',
	);

// -- DEMEURE
$DICO_FNAIM["ETAT_EXTERIEUR"] = 
	array(
		81 => 'Sompteux',
		82 => 'Trés bon',
		83 => 'Bon',
		84 => 'Travaux à prévoir',
		85 => 'A réhabiliter',
		86 => 'A refraîchir',
	);

// -- DEMEURE
$DICO_FNAIM["CLASSEMENT"] = 
	array(
		87 => 'Monument Historique',
		88 => 'Inventaire Supplémentaire Monument Historique',
	);
// <--------------------->

// <--------------------->
class FNAIM_AGENCE {
   var $ADH_NUM;
   var $NEGOCIATEUR;
   function FNAIM_AGENCE($ADH_NUM = NULL) {
		$this->ADH_NUM = $ADH_NUM;
		$this->NEGOCIATEUR = array();
   }
   function addNegociateur(&$NEGOCIATEUR) {
		$this->NEGOCIATEUR[] = $NEGOCIATEUR;
   }
} 
// <--------------------->
class FNAIM_NEGOCIATEUR {
   var $NUM;
   var $CIVILITE;
   var $NOM;
   var $PRENOM;
   var $TELEPHONE;
   var $PORTABLE;
   var $EMAIL;
   function FNAIM_NEGOCIATEUR($NUM = NULL, $CIVILITE = NULL, $NOM = NULL, $PRENOM = NULL, $TELEPHONE = NULL, $PORTABLE = NULL, $EMAIL = NULL) {
       $this->NUM 		= $NUM;
       $this->CIVILITE 	= $CIVILITE;
       $this->NOM 		= $NOM;
       $this->PRENOM 	= $PRENOM;
       $this->TELEPHONE = $TELEPHONE;
       $this->PORTABLE 	= $PORTABLE;
       $this->EMAIL 	= $EMAIL;
   }
} 
// <--------------------->
class FNAIM_BIEN 
{
   function FNAIM_BIEN($INFO_GENERALES=NULL,$action="") 
   {
   		if(!empty($action))
   			$this->PARAMS["action"] = strtoupper($action);
		if($INFO_GENERALES != NULL)
   			$this->add("INFO_GENERALES",$INFO_GENERALES);
		if(strtolower($action) == "s")
   			$this->add("ARCHIVAGE");
			
   }
   // ---
   function add() 
   {
   		$listargs = func_get_args();
		if(count($listargs) == 0) return;
		$TITRE = strtoupper($listargs[0]); 
		unset($listargs[0]);
		if(class_exists("FNAIM_BIEN_" . $TITRE))
		{
			eval(' $this->$TITRE = &new FNAIM_BIEN_' . $TITRE . '();');
			if(count($listargs) > 0)
				call_user_func_array(array(&$this->$TITRE,"FNAIM_BIEN_" . $TITRE),$listargs) ;
		}
		return;
   }  
} 
// <--------------------->
class FNAIM_BIEN_PROPRIETAIRE
{
	var $PRO_NUM;
	var $CIVILITE;
	var $NOM;
	var $PRENOM;
	var $ADRESSE1;
	var $ADRESSE2;
	var $CP;
	var $CODE_INSEE;
	var $VILLE;
	var $PAYS;
	var $TELEPHONE_DOMICILE;
	var $TELEPHONE_BUREAU;
	var $TELEPHONE_PORTABLE;
	var $EMAIL;
	var $COMMENTAIRES;
    function FNAIM_BIEN_PROPRIETAIRE($PRO_NUM = NULL, $CIVILITE = NULL, $NOM = NULL, $PRENOM = NULL, $ADRESSE1 = NULL, $ADRESSE2 = NULL, $CP = NULL, $CODE_INSEE = NULL, $VILLE = NULL, $PAYS = NULL, $TELEPHONE_DOMICILE = NULL, $TELEPHONE_BUREAU = NULL, $TELEPHONE_PORTABLE=NULL, $EMAIL=NULL, $COMMENTAIRES=NULL) 
    {
		$this->PRO_NUM = $PRO_NUM;
		$this->CIVILITE = $CIVILITE;
		$this->NOM = $NOM;
		$this->PRENOM = $PRENOM;
		$this->ADRESSE1 = $ADRESSE1;
		$this->ADRESSE2 = $ADRESSE2;
		$this->CP = $CP;
		$this->CODE_INSEE = $CODE_INSEE;
		$this->VILLE = $VILLE;
		$this->PAYS = $PAYS;
		$this->TELEPHONE_DOMICILE = $TELEPHONE_DOMICILE;
		$this->TELEPHONE_BUREAU = $TELEPHONE_BUREAU;
		$this->TELEPHONE_PORTABLE = $TELEPHONE_PORTABLE;
		$this->EMAIL = $EMAIL;
		$this->COMMENTAIRES = $COMMENTAIRES;
   	}
} 
// <--------------------->
class FNAIM_BIEN_INFO_GENERALES 
{
	var $ADH_NUM;
	var $AFF_NUM;
	var $DATE_CREATION;
	var $DATE_ECHEANCE;
	var $TRANSFERT_PUBLIC;
	var $CODE_NEGOCIATEUR;
	var $VISIBLE_TOUTES_AGENCES;
    function FNAIM_BIEN_INFO_GENERALES($ADH_NUM = NULL, $AFF_NUM = NULL, $DATE_CREATION = '99/99/9999', $DATE_ECHEANCE = '99/99/9999', $TRANSFERT_PUBLIC = 1, $CODE_NEGOCIATEUR = 0, $VISIBLE_TOUTES_AGENCES = NULL) 
    {
		$this->ADH_NUM 				= $ADH_NUM;
		$this->AFF_NUM 				= $AFF_NUM;
		$this->DATE_CREATION 		= $DATE_CREATION;
		$this->DATE_ECHEANCE 		= $DATE_ECHEANCE;
		$this->TRANSFERT_PUBLIC 	= $TRANSFERT_PUBLIC;
		$this->CODE_NEGOCIATEUR 	= $CODE_NEGOCIATEUR;
		$this->VISIBLE_TOUTES_AGENCES = $VISIBLE_TOUTES_AGENCES;
   	}
} 
// <--------------------->
class FNAIM_BIEN_VENTE 
{
	var $ESTIMATION;
	var $PRIX;
	var $HONORAIRES;
	var $NUM_MANDAT;
	var $TYPE_MANDAT;			/* S -> Simple | E -> Exclusif | P -> Privilégié */
	var $DATE_MANDAT;
	var $PASSE_SOUS_COMPROMIS;
	var $DATE_COMPROMIS;
	var $TAXE_HABITATION;
	var $TAXE_FONCIERE;
	var $CHARGES_MENSUELLES;
	var $REGIME_FISCAL;
	
    function FNAIM_BIEN_VENTE($ESTIMATION = NULL, $PRIX = 0, $HONORAIRES = NULL, $NUM_MANDAT = 0, $TYPE_MANDAT = 'S', $DATE_MANDAT = NULL, $PASSE_SOUS_COMPROMIS = NULL, $DATE_COMPROMIS = NULL, $TAXE_HABITATION = NULL, $TAXE_FONCIERE = NULL, $CHARGES_MENSUELLES = NULL, $REGIME_FISCAL = NULL) 
    {
		$this->ESTIMATION 			= $ESTIMATION;
		$this->PRIX 				= $PRIX;
		$this->HONORAIRES 			= $HONORAIRES;
		$this->NUM_MANDAT 			= $NUM_MANDAT;
		$this->TYPE_MANDAT 			= $TYPE_MANDAT;
		$this->DATE_MANDAT 			= $DATE_MANDAT;
		$this->PASSE_SOUS_COMPROMIS = $PASSE_SOUS_COMPROMIS;
		$this->DATE_COMPROMIS 		= $DATE_COMPROMIS;
		$this->TAXE_HABITATION 		= $TAXE_HABITATION;
		$this->TAXE_FONCIERE 		= $TAXE_FONCIERE;
		$this->CHARGES_MENSUELLES 	= $CHARGES_MENSUELLES;
		$this->REGIME_FISCAL 		= $REGIME_FISCAL;
   	}
} 
// <--------------------->
class FNAIM_BIEN_LOCATION 
{
	var $LOYER;
	var $DEPOT_GARANTIE;
	var $FRAIS_DIVERS;
	var $ETAT_LIEUX;
	var $FRAIS_AGENCE;
	var $HONORAIRES_BAIL_LOCATAIRE;
	var $HONORAIRES_BAIL_PROPRIETAIRE;
	var $HONORAIRES_LOCATION_LOCATAIRE;
	var $HONORAIRES_LOCATION_PROPRIETAIRE;
	var $HONORAIRES_ETAT_LOCATAIRE;
	var $HONORAIRES_ETAT_PROPRIETAIRE;
	var $HONORAIRES_TOTAL_LOCATAIRE;
	var $HONORAIRES_TOTAL_PROPRIETAIREE;
	var $CAUTION;
	var $PROVISION_SUR_CHARGES;
	var $DUREE_BAIL;
	var $LIBRE_LE;
	var $TYPE_MANDAT;
	var $NUM_MANDAT;
	var $DATE_MANDAT;
	var $DATE_COMPROMIS;
	var $OCCUPE_PAR;
	var $LOYER_ACTUEL;
	var $DATE_FIN_BAIL;
	var $TELEPHONE_LOC_ACTUEL;
		
    function FNAIM_BIEN_VENTE($LOYER = NULL, $DEPOT_GARANTIE = NULL, $FRAIS_DIVERS = NULL, $ETAT_LIEUX = NULL, $FRAIS_AGENCE = NULL, $HONORAIRES_BAIL_LOCATAIRE = NULL, $HONORAIRES_BAIL_PROPRIETAIRE = NULL, $HONORAIRES_LOCATION_LOCATAIRE = NULL, $HONORAIRES_LOCATION_PROPRIETAIRE = NULL, $HONORAIRES_ETAT_LOCATAIRE = NULL, $HONORAIRES_ETAT_PROPRIETAIRE = NULL, $HONORAIRES_TOTAL_LOCATAIRE = NULL, $HONORAIRES_TOTAL_PROPRIETAIREE = NULL, $CAUTION = NULL, $PROVISION_SUR_CHARGES = NULL, $DUREE_BAIL = NULL, $LIBRE_LE = NULL, $TYPE_MANDAT = NULL, $NUM_MANDAT = NULL, $DATE_MANDAT = NULL, $DATE_COMPROMIS = NULL, $OCCUPE_PAR = NULL, $LOYER_ACTUEL = NULL, $DATE_FIN_BAIL = NULL, $TELEPHONE_LOC_ACTUEL = NULL) 
    {
		$this->LOYER = $LOYER;		
		$this->DEPOT_GARANTIE = $DEPOT_GARANTIE;		
		$this->FRAIS_DIVERS = $FRAIS_DIVERS;		
		$this->ETAT_LIEUX = $ETAT_LIEUX;		
		$this->FRAIS_AGENCE = $FRAIS_AGENCE;		
		$this->HONORAIRES_BAIL_LOCATAIRE = $HONORAIRES_BAIL_LOCATAIRE;		
		$this->HONORAIRES_BAIL_PROPRIETAIRE = $HONORAIRES_BAIL_PROPRIETAIRE;		
		$this->HONORAIRES_LOCATION_LOCATAIRE = $HONORAIRES_LOCATION_LOCATAIRE;		
		$this->HONORAIRES_LOCATION_PROPRIETAIRE = $HONORAIRES_LOCATION_PROPRIETAIRE;		
		$this->HONORAIRES_ETAT_LOCATAIRE = $HONORAIRES_ETAT_LOCATAIRE;		
		$this->HONORAIRES_ETAT_PROPRIETAIRE = $HONORAIRES_ETAT_PROPRIETAIRE;		
		$this->HONORAIRES_TOTAL_LOCATAIRE = $HONORAIRES_TOTAL_LOCATAIRE;		
		$this->HONORAIRES_TOTAL_PROPRIETAIREE = $HONORAIRES_TOTAL_PROPRIETAIREE;		
		$this->CAUTION = $CAUTION;		
		$this->PROVISION_SUR_CHARGES = $PROVISION_SUR_CHARGES;		
		$this->DUREE_BAIL = $DUREE_BAIL;		
		$this->LIBRE_LE = $LIBRE_LE;		
		$this->TYPE_MANDAT = $TYPE_MANDAT;		
		$this->NUM_MANDAT = $NUM_MANDAT;		
		$this->DATE_MANDAT = $DATE_MANDAT;		
		$this->DATE_COMPROMIS = $DATE_COMPROMIS;		
		$this->OCCUPE_PAR = $OCCUPE_PAR;		
		$this->LOYER_ACTUEL = $LOYER_ACTUEL;		
		$this->DATE_FIN_BAIL = $DATE_FIN_BAIL;		
		$this->TELEPHONE_LOC_ACTUEL = $TELEPHONE_LOC_ACTUEL;
   	}
} 
// <--------------------->
class FNAIM_BIEN_VIAGER 
{
	var $NOMBRE_TETES;
	var $BOUQUET;
	var $RENTE;
	var $DATE_MANDAT;
	var $NUM_MANDAT;
	var $TYPE_MANDAT;
	var $DATE_COMPROMIS;
		
    function FNAIM_BIEN_VIAGER($NOMBRE_TETES = NULL, $BOUQUET = NULL, $RENTE = NULL, $DATE_MANDAT = NULL, $NUM_MANDAT = NULL, $TYPE_MANDAT = NULL, $DATE_COMPROMIS = NULL) 
    {
		$this->NOMBRE_TETES = $NOMBRE_TETES;		
		$this->BOUQUET = $BOUQUET;		
		$this->RENTE = $RENTE;		
		$this->DATE_MANDAT = $DATE_MANDAT;		
		$this->NUM_MANDAT = $NUM_MANDAT;		
		$this->TYPE_MANDAT = $TYPE_MANDAT;		
		$this->DATE_COMPROMIS = $DATE_COMPROMIS;
   	}
} 
// <--------------------->
class FNAIM_BIEN_MAISON 
{
	var $NBRE_PIECES;
	var $NBRE_CHAMBRES;
	var $SURFACE_HABITABLE;
	var $SURFACE_SEJOUR;
	var $SURFACE_TERRAIN;
	var $NBRE_NIVEAUX;
	var $ANNEE_CONSTRUCTION;
	var $SURFACE_DEPENDANCE;
	var $CATEGORIE;
	var $ETAT_GENERAL;			/* 68 -> Habitable | 69 -> Travaux à prévoir | 70 -> Trés bon état */
	var $NBRE_SALLE_BAIN;
	var $NBRE_GARAGE;
	var $NBRE_BALCON;
	var $NBRE_SALLE_EAU;
	var $NBRE_PARKING;
	var $NBRE_TERRASSE;
	var $CUISINE;
	var $SOUS_SOL;
	var $CONSTRUCTION;			/* 41 -> Pierre | 42 -> Brique | 43 -> Parpaing | 44 -> Béton | 45 -> Bois | 46 -> Meulière | 47 -> Colombage */
	var $MITOYENNETE;			/* 54 -> Indépendant | 55 -> 1 côté | 56 -> 2 côtés | 57 -> 3 côtés */
	var $STANDING;				/* 1 -> Normal | 2 -> Bon | 3 -> Grand standing */
	var $MEUBLE;
	var $CONVERTURE;			/* 48 -> Terrasse | 49 -> Tuiles | 50 -> Ardoise | 51 -> Chaumes | 52 -> Autre | 53 -> Tôle */
	var $LOCALISATION;
	var $LOTISSEMENT;
	var $EXPOSITION;
	var $CHAUFFAGE;
	var $MECANISME_CHAUFFAGE;
	var $MODE_CHAUFFAGE;
		
    function FNAIM_BIEN_MAISON($NBRE_PIECES = NULL, $NBRE_CHAMBRES = NULL, $SURFACE_HABITABLE = NULL, $SURFACE_SEJOUR = NULL, $SURFACE_TERRAIN = NULL, $NBRE_NIVEAUX = NULL, $ANNEE_CONSTRUCTION = NULL, $SURFACE_DEPENDANCE = NULL, $CATEGORIE = NULL, $ETAT_GENERAL = NULL, $NBRE_SALLE_BAIN = NULL, $NBRE_GARAGE = NULL, $NBRE_BALCON = NULL, $NBRE_SALLE_EAU = NULL, $NBRE_PARKING = NULL, $NBRE_TERRASSE = NULL, $CUISINE = NULL, $SOUS_SOL = NULL, $CONSTRUCTION = NULL, $MITOYENNETE = NULL, $STANDING = NULL, $MEUBLE = NULL, $CONVERTURE = NULL, $LOCALISATION = NULL, $LOTISSEMENT = NULL, $EXPOSITION = NULL, $CHAUFFAGE = NULL, $MECANISME_CHAUFFAGE = NULL, $MODE_CHAUFFAGE = NULL) 
    {
		$this->NBRE_PIECES = $NBRE_PIECES;
		$this->NBRE_CHAMBRES = $NBRE_CHAMBRES;
		$this->SURFACE_HABITABLE = $SURFACE_HABITABLE;
		$this->SURFACE_SEJOUR = $SURFACE_SEJOUR;
		$this->SURFACE_TERRAIN = $SURFACE_TERRAIN;
		$this->NBRE_NIVEAUX = $NBRE_NIVEAUX;
		$this->ANNEE_CONSTRUCTION = $ANNEE_CONSTRUCTION;
		$this->SURFACE_DEPENDANCE = $SURFACE_DEPENDANCE;
		$this->CATEGORIE = $CATEGORIE;
		$this->ETAT_GENERAL = $ETAT_GENERAL;
		$this->NBRE_SALLE_BAIN = $NBRE_SALLE_BAIN;
		$this->NBRE_GARAGE = $NBRE_GARAGE;
		$this->NBRE_BALCON = $NBRE_BALCON;
		$this->NBRE_SALLE_EAU = $NBRE_SALLE_EAU;
		$this->NBRE_PARKING = $NBRE_PARKING;
		$this->NBRE_TERRASSE = $NBRE_TERRASSE;
		$this->CUISINE = $CUISINE;
		$this->SOUS_SOL = $SOUS_SOL;
		$this->CONSTRUCTION = $CONSTRUCTION;
		$this->MITOYENNETE = $MITOYENNETE;
		$this->STANDING = $STANDING;
		$this->MEUBLE = $MEUBLE;
		$this->CONVERTURE = $CONVERTURE;
		$this->LOCALISATION = $LOCALISATION;
		$this->LOTISSEMENT = $LOTISSEMENT;
		$this->EXPOSITION = $EXPOSITION;
		$this->CHAUFFAGE = $CHAUFFAGE;
		$this->MECANISME_CHAUFFAGE = $MECANISME_CHAUFFAGE;
		$this->MODE_CHAUFFAGE = $MODE_CHAUFFAGE;
   	}
} 
// <--------------------->
class FNAIM_BIEN_APPARTEMENT 
{
	var $NUM_ETAGE;
	var $NUM_DERNIER_ETAGE;
	var $SURFACE_HABITABLE;
	var $SURFACE_SEJOUR;
	var $NBRE_PIECES;
	var $NBRE_CHAMBRES;
	var $ANNEE_CONSTRUCTION;
	var $TERRAIN_PRIVATIF;
	var $NBRE_NIVEAUX;
	var $CATEGORIE;
	var $ETAT_GENERAL;
	var $NBRE_SALLE_BAIN;
	var $NBRE_GARAGE;
	var $NBRE_BALCON;
	var $NBRE_SALLE_EAU;
	var $NBRE_PARKING;
	var $NBRE_TERRASSE;
	var $CUISINE;				/* 18 -> Aménagée | 19 -> Equipée | 20 -> Kitchenette | 337 -> Simple | 337 -> sans cuisine */
	var $EAU_CHAUDE;
	var $ACCES_HANDICAPES;
	var $ASCENSEUR;
	var $STANDING;
	var $MEUBLE;
	var $NBRE_CAVES;
	var $LOCALISATION;
	var $EXPOSITION;
	var $CHAUFFAGE;
	var $MECANISME_CHAUFFAGE;
	var $MODE_CHAUFFAGE;
		
    function FNAIM_BIEN_APPARTEMENT($NUM_ETAGE = NULL, $NUM_DERNIER_ETAGE = NULL, $SURFACE_HABITABLE = NULL, $SURFACE_SEJOUR = NULL, $NBRE_PIECES = NULL, $NBRE_CHAMBRES = NULL, $ANNEE_CONSTRUCTION = NULL, $TERRAIN_PRIVATIF = NULL, $NBRE_NIVEAUX = NULL, $CATEGORIE = NULL, $ETAT_GENERAL = NULL, $NBRE_SALLE_BAIN = NULL, $NBRE_GARAGE = NULL, $NBRE_BALCON = NULL, $NBRE_SALLE_EAU = NULL, $NBRE_PARKING = NULL, $NBRE_TERRASSE = NULL, $CUISINE = NULL, $EAU_CHAUDE = NULL, $ACCES_HANDICAPES = NULL, $ASCENSEUR = NULL, $STANDING = NULL, $MEUBLE = NULL, $NBRE_CAVES = NULL, $LOCALISATION = NULL, $EXPOSITION = NULL, $CHAUFFAGE = NULL, $MECANISME_CHAUFFAGE = NULL, $MODE_CHAUFFAGE = NULL) 
    {
		$this->NUM_ETAGE = $NUM_ETAGE;
		$this->NUM_DERNIER_ETAGE = $NUM_DERNIER_ETAGE;
		$this->SURFACE_HABITABLE = $SURFACE_HABITABLE;
		$this->SURFACE_SEJOUR = $SURFACE_SEJOUR;
		$this->NBRE_PIECES = $NBRE_PIECES;
		$this->NBRE_CHAMBRES = $NBRE_CHAMBRES;
		$this->ANNEE_CONSTRUCTION = $ANNEE_CONSTRUCTION;
		$this->TERRAIN_PRIVATIF = $TERRAIN_PRIVATIF;
		$this->NBRE_NIVEAUX = $NBRE_NIVEAUX;
		$this->CATEGORIE = $CATEGORIE;
		$this->ETAT_GENERAL = $ETAT_GENERAL;
		$this->NBRE_SALLE_BAIN = $NBRE_SALLE_BAIN;
		$this->NBRE_GARAGE = $NBRE_GARAGE;
		$this->NBRE_BALCON = $NBRE_BALCON;
		$this->NBRE_SALLE_EAU = $NBRE_SALLE_EAU;
		$this->NBRE_PARKING = $NBRE_PARKING;
		$this->NBRE_TERRASSE = $NBRE_TERRASSE;
		$this->CUISINE = $CUISINE;
		$this->EAU_CHAUDE = $EAU_CHAUDE;
		$this->ACCES_HANDICAPES = $ACCES_HANDICAPES;
		$this->ASCENSEUR = $ASCENSEUR;
		$this->STANDING = $STANDING;
		$this->MEUBLE = $MEUBLE;
		$this->NBRE_CAVES = $NBRE_CAVES;
		$this->LOCALISATION = $LOCALISATION;
		$this->EXPOSITION = $EXPOSITION;
		$this->CHAUFFAGE = $CHAUFFAGE;
		$this->MECANISME_CHAUFFAGE = $MECANISME_CHAUFFAGE;
		$this->MODE_CHAUFFAGE = $MODE_CHAUFFAGE;
   	}
} 
// <--------------------->
class FNAIM_BIEN_DEMEURE
{
	var $NBRE_PIECES;
	var $NBRE_CHAMBRES;
	var $SURFACE_HABITABLE;
	var $SURFACE_SEJOUR;
	var $ETAT_INTERIEUR;
	var $ETAT_EXTERIEUR;
	var $SURFACE_DEPENDANCE;
	var $SURFACE_PARC;
	var $EPOQUE;
	var $SURFACE_AUTRE_TERRAIN;
	var $SURFACE_BOIS;
	var $SURFACE_TERRE;
	var $SURFACE_ETANG;
	var $LONGUEUR_RIVIERE;
	var $DISTANCE_TRAIN;
	var $DISTANCE_AEROPORT;
	var $DISTANCE_AUTOROUTE;
	var $DISTANCE_COMMODITES;
	var $CATEGORIE;
	var $NBRE_SALLE_BAIN;
	var $NBRE_GARAGE;
	var $NBRE_NIVEAU;
	var $NBRE_BALCON;
	var $NBRE_SALLE_EAU;
	var $NBRE_PARKING;
	var $NBRE_TERRASSE;
	var $CUISINE;
	var $EAU_CHAUDE;
	var $SOUS_SOL;
	var $CONSTRUCTION;
	var $MITOYENNETE;
	var $ISOLATION;
	var $COUVERTURE;
	var $LOCALISATION;
	var $CLASSEMENT;
	var $CHAUFFAGE;
	var $MECANISME_CHAUFFAGE;
	var $MODE_CHAUFFAGE;
	var $DESCRIPTION_PISCINE;
	var $DESCRIPTION_TENNIS;
	var $LOGEMENT_GARDIEN;
	var $PROPRIETE_DEMEURE;
	var $AGREMENT_VUE;
	var $AGREMENT_CHASSE;
	var $AGREMENT_GOLF;
	var $AGREMENT_LAC;
	var $AGREMENT_MONTAGNE;
	var $AGREMENT_MER;
		
    function FNAIM_BIEN_DEMEURE($NBRE_PIECES = NULL, $NBRE_CHAMBRES = NULL, $SURFACE_HABITABLE = NULL, $SURFACE_SEJOUR = NULL, $ETAT_INTERIEUR = NULL, $ETAT_EXTERIEUR = NULL, $SURFACE_DEPENDANCE = NULL, $SURFACE_PARC = NULL, $EPOQUE = NULL, $SURFACE_AUTRE_TERRAIN = NULL, $SURFACE_BOIS = NULL, $SURFACE_TERRE = NULL, $SURFACE_ETANG = NULL, $LONGUEUR_RIVIERE = NULL, $DISTANCE_TRAIN = NULL, $DISTANCE_AEROPORT = NULL, $DISTANCE_AUTOROUTE = NULL, $DISTANCE_COMMODITES = NULL, $CATEGORIE = NULL, $NBRE_SALLE_BAIN = NULL, $NBRE_GARAGE = NULL, $NBRE_NIVEAU = NULL, $NBRE_BALCON = NULL, $NBRE_SALLE_EAU = NULL, $NBRE_PARKING = NULL, $NBRE_TERRASSE = NULL, $CUISINE = NULL, $EAU_CHAUDE = NULL, $SOUS_SOL = NULL, $CONSTRUCTION = NULL, $MITOYENNETE = NULL, $ISOLATION = NULL, $COUVERTURE = NULL, $LOCALISATION = NULL, $CLASSEMENT = NULL, $CHAUFFAGE = NULL, $MECANISME_CHAUFFAGE = NULL, $MODE_CHAUFFAGE = NULL, $DESCRIPTION_PISCINE = NULL, $DESCRIPTION_TENNIS = NULL, $LOGEMENT_GARDIEN = NULL, $PROPRIETE_DEMEURE = NULL, $AGREMENT_VUE = NULL, $AGREMENT_CHASSE = NULL, $AGREMENT_GOLF = NULL, $AGREMENT_LAC = NULL, $AGREMENT_MONTAGNE = NULL, $AGREMENT_MER = NULL) 
    {
		$this->NBRE_PIECES 				= $NBRE_PIECES;
		$this->NBRE_CHAMBRES 			= $NBRE_CHAMBRES;
		$this->SURFACE_HABITABLE 		= $SURFACE_HABITABLE;
		$this->SURFACE_SEJOUR 			= $SURFACE_SEJOUR;
		$this->ETAT_INTERIEUR 			= $ETAT_INTERIEUR;
		$this->ETAT_EXTERIEUR 			= $ETAT_EXTERIEUR;
		$this->SURFACE_DEPENDANCE 		= $SURFACE_DEPENDANCE;
		$this->SURFACE_PARC 			= $SURFACE_PARC;
		$this->EPOQUE 					= $EPOQUE;
		$this->SURFACE_AUTRE_TERRAIN 	= $SURFACE_AUTRE_TERRAIN;
		$this->SURFACE_BOIS 			= $SURFACE_BOIS;
		$this->SURFACE_TERRE 			= $SURFACE_TERRE;
		$this->SURFACE_ETANG 			= $SURFACE_ETANG;
		$this->LONGUEUR_RIVIERE 		= $LONGUEUR_RIVIERE;
		$this->DISTANCE_TRAIN 			= $DISTANCE_TRAIN;
		$this->DISTANCE_AEROPORT 		= $DISTANCE_AEROPORT;
		$this->DISTANCE_AUTOROUTE 		= $DISTANCE_AUTOROUTE;
		$this->DISTANCE_COMMODITES 		= $DISTANCE_COMMODITES;
		$this->CATEGORIE 				= $CATEGORIE;
		$this->NBRE_SALLE_BAIN 			= $NBRE_SALLE_BAIN;
		$this->NBRE_GARAGE 				= $NBRE_GARAGE;
		$this->NBRE_NIVEAU 				= $NBRE_NIVEAU;
		$this->NBRE_BALCON 				= $NBRE_BALCON;
		$this->NBRE_SALLE_EAU 			= $NBRE_SALLE_EAU;
		$this->NBRE_PARKING 			= $NBRE_PARKING;
		$this->NBRE_TERRASSE 			= $NBRE_TERRASSE;
		$this->CUISINE 					= $CUISINE;
		$this->EAU_CHAUDE 				= $EAU_CHAUDE;
		$this->SOUS_SOL 				= $SOUS_SOL;
		$this->CONSTRUCTION 			= $CONSTRUCTION;
		$this->MITOYENNETE 				= $MITOYENNETE;
		$this->ISOLATION 				= $ISOLATION;
		$this->COUVERTURE 				= $COUVERTURE;
		$this->LOCALISATION 			= $LOCALISATION;
		$this->CLASSEMENT 				= $CLASSEMENT;
		$this->CHAUFFAGE 				= $CHAUFFAGE;
		$this->MECANISME_CHAUFFAGE 		= $MECANISME_CHAUFFAGE;
		$this->MODE_CHAUFFAGE 			= $MODE_CHAUFFAGE;
		$this->DESCRIPTION_PISCINE 		= $DESCRIPTION_PISCINE;
		$this->DESCRIPTION_TENNIS 		= $DESCRIPTION_TENNIS;
		$this->LOGEMENT_GARDIEN 		= $LOGEMENT_GARDIEN;
		$this->PROPRIETE_DEMEURE 		= $PROPRIETE_DEMEURE;
		$this->AGREMENT_VUE 			= $AGREMENT_VUE;
		$this->AGREMENT_CHASSE 			= $AGREMENT_CHASSE;
		$this->AGREMENT_GOLF 			= $AGREMENT_GOLF;
		$this->AGREMENT_LAC 			= $AGREMENT_LAC;
		$this->AGREMENT_MONTAGNE 		= $AGREMENT_MONTAGNE;
		$this->AGREMENT_MER 			= $AGREMENT_MER;
   	}
} 
// <--------------------->
class FNAIM_BIEN_LOCALISATION
{
	var $CODE_POSTAL;
	var $CODE_INSEE;
	var $NUM_RUE;
	var $BIS_RUE;
	var $TYPE_RUE;
	var $NOM_RUE;
	var $RESIDENCE;
	var $QUARTIER;
	var $LOCALISATION;
	var $VISIBLE;
		
    function FNAIM_BIEN_LOCALISATION($CODE_POSTAL = NULL, $VILLE = NULL, $CODE_INSEE = NULL, $NUM_RUE = NULL, $BIS_RUE = NULL, $TYPE_RUE = NULL, $NOM_RUE = NULL, $RESIDENCE = NULL, $QUARTIER = NULL, $SITUATION = NULL, $VISIBLE = NULL) 
    {
		$this->CODE_POSTAL = $CODE_POSTAL;
		$this->VILLE = $VILLE;
		$this->CODE_INSEE = $CODE_INSEE;
		$this->NUM_RUE = $NUM_RUE;
		$this->BIS_RUE = $BIS_RUE;
		$this->TYPE_RUE = $TYPE_RUE;
		$this->NOM_RUE = $NOM_RUE;
		$this->RESIDENCE = $RESIDENCE;
		$this->QUARTIER = $QUARTIER;
		$this->SITUATION = $SITUATION;
		$this->VISIBLE = $VISIBLE;
   	}
} 
// <--------------------->
class FNAIM_BIEN_COMMENTAIRES
{
	var $FR;
	var $US;
	var $DE;
	var $ES;
	var $IT;
		
    function FNAIM_BIEN_COMMENTAIRES($FR = NULL, $US = NULL, $DE = NULL, $ES = NULL, $IT = NULL) 
    {
		$this->FR = $FR;
		$this->US = $US;
		$this->DE = $DE;
		$this->ES = $ES;
		$this->IT = $IT;
   	}
} 
// <--------------------->
class FNAIM_BIEN_IMAGES
{
	var $IMG;
	function FNAIM_BIEN_IMAGES($IMG = NULL)
	{ 
		$this->IMG = $IMG; 
	}
	function ADD()
	{ 
		$listargs = func_get_args();
		$TEMP = &new FNAIM_BIEN_IMG();
		call_user_func_array(array(&$TEMP,"FNAIM_BIEN_IMG"),$listargs) ;
		$this->IMG[] = $TEMP; 
	}
} 
// <--------------------->
class FNAIM_BIEN_IMG
{
	var $PARAMS;
	var $DATA;
	function FNAIM_BIEN_IMG($numAffaire=0,$FICHIER_IMAGE=false,$loginFNAIM=0,$commentaire='')
	{
		global $FRAMEWORK;
		global $NB_FNAIM_PHOTOS;
		
		$IMAGE_BASE64 = "/9j/4AAQSkZJRgABAQAAAQABAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gNzUK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgAAQABAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A9/ooooA//9k="; 
		if($FICHIER_IMAGE && file_exists($FICHIER_IMAGE)) {
			vendor("phpThumb/phpthumb.class");
			$phpThumb = new phpThumb();
			$phpThumb->w = 400;
			$phpThumb->h = 300;
			$phpThumb->zc = 1;
			$phpThumb->config_output_format = "jpg";
			$phpThumb->config_error_die_on_error = false;
			$phpThumb->config_allow_src_above_docroot = true;
			$phpThumb->src = $FICHIER_IMAGE;
			$phpThumb->GenerateThumbnail();
			if($phpThumb->RenderOutput())
				$IMAGE_BASE64 = base64_encode($phpThumb->outputImageData);
		}
		
		$numeroPhoto = ++$NB_FNAIM_PHOTOS[$numAffaire];
		$this->PARAMS = array();
		$this->PARAMS["commentaire"] = $commentaire;
		$this->PARAMS["nom"] = $this->returnImgNom($loginFNAIM,$numAffaire,$numeroPhoto);
		$this->PARAMS["num"] = $numeroPhoto;
		$this->DATA = $IMAGE_BASE64;
		
		return true;
	}
	// ------------
	function returnImgNom($loginFNAIM,$numAffaire,$numeroPhoto)
	{
		return str_pad($loginFNAIM, 5, "0", STR_PAD_LEFT).str_pad($numAffaire, 9, "0", STR_PAD_LEFT)."T".str_pad($numeroPhoto, 2, "0", STR_PAD_LEFT);
	}
} 
// <--------------------->
class FNAIM_BIEN_ARCHIVAGE 
{
	var $MOTIF_SUPPRESSION = 2;
	var $PRIX_VENTE = 0;
}
// <--------------------->
// class FNAIM_BIEN_DEMEURE 
// class FNAIM_BIEN_TERRAIN
// class FNAIM_BIEN_PARKING
// class FNAIM_BIEN_IMMEUBLE
// class FNAIM_BIEN_AGRICOLE_VITICOLE
// class FNAIM_BIEN_FORET
// class FNAIM_BIEN_LOCAL_COMMERCIAL
// class FNAIM_BIEN_LOCAL_PROFESSIONEL
// class FNAIM_BIEN_LOCAL_INDUSTRIEL
// class FNAIM_BIEN_FOND_COMMERCE
// class FNAIM_BIEN_VACANCES
// class FNAIM_BIEN_CONFIDENTIEL
// class FNAIM_BIEN_PLANNING
// class FNAIM_BIEN_TARIFICATION
// class FNAIM_BIEN_ARCHIVAGE
// <--------------------->

class FNAIMIris
{
	var $DATAS;
	// ---------------
	function FNAIMIris()
	{ }
	// ---------------
	function toXML()
	{
		$options = array(
			'indent'			=> "\t",        // indent with tabs
			'addDecl' 			=> TRUE, 
			'mode'               => 'simplexml',  
			'linebreak' 		=> "\n",        // use UNIX line breaks
			'rootName'			=> 'TRANSFERT',   // root tag
			'rootAttributes'	=> array('version'=>'1.0','origine'=>'10'),
			'defaultTagName'	=> 'item',       // tag for values with numeric keys
			'attributesArray'	=> 'PARAMS',                    
			'encoding' 			=> 'ISO-8859-1',
   		);
		
		$serializer = new XML_Serializer($options);
 	  	$serializer->serialize($this->DATAS);
		$XML_DATAS = $serializer->getSerializedData();
		return $XML_DATAS;
	}
	// ------------------
	function loadXML($XML)
	{
		// Array of options
		$unserializer_options = array (
		   'parseAttributes' => TRUE,
		   'attributesArray' => 'PARAMS'
		);
		// Instantiate the serializer
		$unserializer = &new XML_Unserializer($unserializer_options);
		// Serialize the data structure
		$status = $unserializer->unserialize($XML); 
		$INFOS_ARRAY = $unserializer->getUnserializedData();
		return $INFOS_ARRAY;
	}
	// ------------------
}
?>