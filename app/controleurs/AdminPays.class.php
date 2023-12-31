<?php

/**
 * Classe Contrôleur des requêtes sur l'entité Pays de l'application admin
 */

class AdminPays extends Admin {

  protected $methodes = [
    'l'  => ['nom' => 'listerPays',        'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR, Utilisateur::PROFIL_CORRECTEUR]],
    'a'  => ['nom' => 'ajouterPays',       'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR]],
    'm'  => ['nom' => 'modifierPays',      'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR, Utilisateur::PROFIL_CORRECTEUR]],
    's'  => ['nom' => 'supprimerPays',     'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR]],
    'lf' => ['nom' => 'listerPaysFilm',    'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR, Utilisateur::PROFIL_CORRECTEUR]],
    'af' => ['nom' => 'ajouterPaysFilm',   'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR, Utilisateur::PROFIL_CORRECTEUR]],
    'sf' => ['nom' => 'supprimerPaysFilm', 'droits' => [Utilisateur::PROFIL_ADMINISTRATEUR, Utilisateur::PROFIL_EDITEUR, Utilisateur::PROFIL_CORRECTEUR]]
   ];

  /**
   * Constructeur qui initialise des propriétés à partir du query string
   * et la propriété oRequetesSQL déclarée dans la classe Routeur
   * 
   */
  public function __construct() {
    $this->pays_id = $_GET['pays_id'] ?? null;
    $this->film_id = $_GET['film_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }  

  /**
   * Lister les pays
   */
  public function listerPays() {
    $pays = $this->oRequetesSQL->getPays();
    (new Vue)->generer(
      'vAdminPays',
      [
        'oUtilConn'           => self::$oUtilConn,
        'titre'               => 'Gestion des pays',
        'pays'                => $pays,
        'classRetour'         => $this->classRetour,  
        'messageRetourAction' => $this->messageRetourAction
      ],
      'gabarit-admin');
  }

  /**
   * Lister les pays d'un film
   */
  public function listerPaysFilm() {
    $pays = $this->oRequetesSQL->getPaysFilm($this->film_id, true);
    echo json_encode($pays);
  }

  /**
   * Ajouter un pays
   */
  public function ajouterPays() {
    if (count($_POST) !== 0) {
      $pays = $_POST;
      $oPays = new Pays($pays);
      $erreurs = $oPays->erreurs;
      if (count($erreurs) === 0) {
        $retour = $this->oRequetesSQL->ajouterPays([
          'pays_nom' => $oPays->pays_nom
        ]);
        if (preg_match('/^[1-9]\d*$/', $retour)) {
          $this->messageRetourAction = "Ajout du pays numéro $retour effectué.";
        } else {
          $this->classRetour = "erreur";         
          $this->messageRetourAction = "Ajout du pays non effectué.";
        }
        $this->listerPays();
        exit;
      }
    } else {
      $pays    = [];
      $erreurs = [];
    }
    
    (new Vue)->generer(
      'vAdminPaysAjouter',
      [
        'oUtilConn' => self::$oUtilConn,
        'titre'     => 'Ajouter un pays',
        'pays'      => $pays,
        'erreurs'   => $erreurs
      ],
      'gabarit-admin');
  }

  /**
   * Modifier un pays
   */
  public function modifierPays() {
    if (count($_POST) !== 0) {
      $pays = $_POST;
      $oPays = new Pays($pays);
      $erreurs = $oPays->erreurs;
      if (count($erreurs) === 0) {
        $retour = $this->oRequetesSQL->modifierPays([
          'pays_id'  => $oPays->pays_id, 
          'pays_nom' => $oPays->pays_nom
        ]);
        if ($retour === true)  {
          $this->messageRetourAction = "Modification du pays numéro $this->pays_id effectuée.";    
        } else {  
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Modification du pays numéro $this->pays_id non effectuée.";
        }
        $this->listerPays();
        exit;
      }
    } else {
      $pays    = $this->oRequetesSQL->getUnPays($this->pays_id);
      $erreurs = [];
    }
    
    (new Vue)->generer(
      'vAdminPaysModifier',
      [
        'oUtilConn' => self::$oUtilConn,
        'titre'     => "Modifier le pays numéro $this->pays_id",
        'pays'      => $pays,
        'erreurs'   => $erreurs
      ],
      'gabarit-admin');
  }
  
  /**
   * Supprimer un pays
   */
  public function supprimerPays() {
    $retour = $this->oRequetesSQL->supprimerPays($this->pays_id);
    if ($retour === false) $this->classRetour = "erreur";
    $this->messageRetourAction = "Suppression du pays numéro $this->pays_id ".($retour ? "" : "non ")."effectuée.";
    $this->listerPays();
  }

  /**
   * Ajouter un pays à un film dans la table de jonction film_pays
   */
  public function ajouterPaysFilm() {
    $retour = $this->oRequetesSQL->ajouterPaysFilm(
      [
        'pays_id' => $this->pays_id,
        'film_id' => $this->film_id
      ]);
    echo json_encode($retour);
  }

  /**
   * Supprimer un pays d'un film dans la table de jonction film_pays
   */
  public function supprimerPaysFilm() {
    $retour = $this->oRequetesSQL->supprimerPaysFilm(
      [
        'pays_id' => $this->pays_id,
        'film_id' => $this->film_id
      ]);
    echo json_encode($retour);
  }
}