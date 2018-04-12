<?php
namespace App\Plugin\EventManagement;
class Auteur {
	private $id;
	private $nom;
	private $provenance = null;
	private $statut = true;

	private $dbh = null;

	public function __construct( $idAuteur = null ) {

        if(is_null($this->dbh)) {
            $this->dbh = \App\DB::connect();
        }

		if ( ! is_null( $idAuteur ) ) {
			$this->id = $idAuteur;
			$this->show();
		}
	}

	/**
	 * @return null
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param null $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getNom() {
		return $this->nom;
	}

	/**
	 * @param mixed $nom
	 */
	public function setNom( $nom ) {
		$this->nom = $nom;
	}

	/**
	 * @return null
	 */
	public function getProvenance() {
		return $this->provenance;
	}

	/**
	 * @param null $provenance
	 */
	public function setProvenance( $provenance ) {
		$this->provenance = $provenance;
	}

	/**
	 * @return bool
	 */
	public function getStatut() {
		return $this->statut;
	}

	/**
	 * @param bool $statut
	 */
	public function setStatut( $statut ) {
		$this->statut = $statut;
	}

	/**
	 * @return bool
	 */
	public function createTable() {
		$sql = 'CREATE TABLE IF NOT EXISTS `appoe_plugin_eventManagement_auteurs` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `nom` VARCHAR(255) NOT NULL,
  				`provenance` VARCHAR(150) DEFAULT NULL,
  				`statut` TINYINT(1) NOT NULL DEFAULT 1,
  				`created_at` DATE NOT NULL,
  				`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->execute();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function show() {

		$sql = 'SELECT * FROM appoe_plugin_eventManagement_auteurs WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->execute();

		$count = $stmt->rowCount();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			if ( $count == 1 ) {
				$row = $stmt->fetch( \PDO::FETCH_OBJ );
				$this->feed( $row );

				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * @param $statut
	 *
	 * @return array|bool
	 */
	public function showAll( $statut = true ) {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement_auteurs WHERE statut = :statut ORDER BY created_at DESC';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':statut', $statut );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return $stmt->fetchAll( \PDO::FETCH_OBJ );

		}
	}

	public function notExist() {

		$sql  = 'SELECT * FROM appoe_plugin_eventManagement_auteurs WHERE nom = :nom AND statut  = TRUE';
		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':nom', $this->nom );
		$stmt->execute();
		$count = $stmt->rowCount();
		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return $count == 1 ? false : true;
		}
	}


	/**
	 * @return bool
	 */
	public function save() {

		$sql = 'INSERT INTO appoe_plugin_eventManagement_auteurs (nom, provenance, created_at) 
                VALUES (:nom, :provenance, CURDATE())';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':nom', $this->nom );
		$stmt->bindParam( ':provenance', $this->provenance );
		$stmt->execute();

		$id = $this->dbh->lastInsertId();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			$this->setId( $id );

			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function update() {

		$sql = 'UPDATE appoe_plugin_eventManagement_auteurs SET nom = :nom, provenance = :provenance, statut = :statut WHERE id = :id';

		$stmt = $this->dbh->prepare( $sql );
		$stmt->bindParam( ':id', $this->id );
		$stmt->bindParam( ':nom', $this->nom );
		$stmt->bindParam( ':provenance', $this->provenance );
		$stmt->bindParam( ':statut', $this->statut );
		$stmt->execute();

		$error = $stmt->errorInfo();
		if ( $error[0] != '00000' ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * @return bool
	 */
	public function delete() {
		$this->statut = false;

		if ( $this->update() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Feed class attributs
	 *
	 * @param $data
	 */
	public function feed( $data ) {
		foreach ( $data as $attribut => $value ) {
			$method = 'set' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $attribut ) ) );

			if ( is_callable( array( $this, $method ) ) ) {
				$this->$method( $value );
			}
		}
	}
}