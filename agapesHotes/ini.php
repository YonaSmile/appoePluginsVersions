<?php
define('AGAPESHOTES_PATH', WEB_PLUGIN_PATH . 'agapesHotes/');
define('AGAPESHOTES_URL', WEB_PLUGIN_URL . 'agapesHotes/');

const PLUGIN_TABLES = array(
    'appoe_plugin_agapesHotes_secteurs',
    'appoe_plugin_agapesHotes_sites',
    'appoe_plugin_agapesHotes_etablissements',
    'appoe_plugin_agapesHotes_prestations',
    'appoe_plugin_agapesHotes_prix_prestations',
    'appoe_plugin_agapesHotes_courses',
    'appoe_plugin_agapesHotes_main_courante',
    'appoe_plugin_agapesHotes_main_supplementaire',
    'appoe_plugin_agapesHotes_vivre_crue',
    'appoe_plugin_agapesHotes_inventaire',
    'appoe_plugin_agapesHotes_employes_contrats',
    'appoe_plugin_agapesHotes_secteurs_access',
    'appoe_plugin_agapesHotes_sites_access',
    'appoe_plugin_agapesHotes_pti',
    'appoe_plugin_agapesHotes_pti_details',
    'appoe_plugin_agapesHotes_planning',
    'appoe_plugin_agapesHotes_planning_plus',
    'appoe_plugin_agapesHotes_note_frais',
    'appoe_plugin_agapesHotes_budget',
    'appoe_plugin_agapesHotes_site_meta',
    'appoe_plugin_agapesHotes_achat'
);

const TYPES_NOTE_FRAIS = array(
    1 => 'Denrée Alimentaire',
    2 => 'Non Alimentaire',
    3 => 'Autre achat'
);

const NOM_TYPES_NOTE_FRAIS = array(
    1 => array(
        'type' => 3,
        'code' => '616000',
        'nom' => 'Assurance "usage affaires"'
    ),
    2 => array(
        'type' => 3,
        'code' => '624800',
        'nom' => 'Avion',
    ),
    3 => array(
        'type' => 3,
        'code' => '623400',
        'nom' => 'Cadeau client < 60€ TTC',
    ),
    4 => array(
        'type' => 3,
        'code' => '623400',
        'nom' => 'Cadeau client > 60€ TTC',
    ),
    5 => array(
        'type' => 3,
        'code' => '623400',
        'nom' => 'Cadeaux au personnel',
    ),
    6 => array(
        'type' => 3,
        'code' => '606141',
        'nom' => 'Carburant véhicule utilitaire',
    ),
    7 => array(
        'type' => 1,
        'code' => '601100',
        'nom' => 'Denrées à 20%',
    ),
    8 => array(
        'type' => 1,
        'code' => '601200',
        'nom' => 'Denrées à 5.5%',
    ),
    9 => array(
        'type' => 3,
        'code' => '618100',
        'nom' => 'Documentation / presse',
    ),
    10 => array(
        'type' => 3,
        'code' => '615600',
        'nom' => 'Entretien / réparation bâtiment',
    ),
    11 => array(
        'type' => 3,
        'code' => '606319',
        'nom' => 'Fleurs Musique Ambiance',
    ),
    12 => array(
        'type' => 3,
        'code' => '618100',
        'nom' => 'Fournitures',
    ),
    13 => array(
        'type' => 3,
        'code' => '624800',
        'nom' => 'Frais de déménagement',
    ),
    14 => array(
        'type' => 3,
        'code' => '625100',
        'nom' => 'Frais de logement',
    ),
    15 => array(
        'type' => 3,
        'code' => '625700',
        'nom' => 'Frais de réception',
    ),
    16 => array(
        'type' => 3,
        'code' => '622600',
        'nom' => 'Frais de recrutement',
    ),
    17 => array(
        'type' => 3,
        'code' => '626300',
        'nom' => 'Frais Téléphone Portable',
    ),
    18 => array(
        'type' => 3,
        'code' => '625600',
        'nom' => 'Hôtel',
    ),
    19 => array(
        'type' => 3,
        'code' => '625100',
        'nom' => 'Location de véhicule de tourisme',
    ),
    20 => array(
        'type' => 3,
        'code' => '625100',
        'nom' => 'Location de véhicule utilitaire',
    ),
    21 => array(
        'type' => 3,
        'code' => '625100',
        'nom' => 'Locations diverses d\'exploitation',
    ),
    22 => array(
        'type' => 3,
        'code' => '625100',
        'nom' => 'Parking / péage',
    ),
    23 => array(
        'type' => 3,
        'code' => '606318',
        'nom' => 'Petit matériel',
    ),
    24 => array(
        'type' => 3,
        'code' => '606800',
        'nom' => 'Pharmacie',
    ),
    25 => array(
        'type' => 2,
        'code' => '606316',
        'nom' => 'Produits d\'entretien 20%',
    ),
    26 => array(
        'type' => 2,
        'code' => '606317',
        'nom' => 'Produits d\'entretien 5.5%',
    ),
    27 => array(
        'type' => 1,
        'code' => '601210',
        'nom' => 'Boissons 5.5%',
    ),
    28 => array(
        'type' => 1,
        'code' => '601110',
        'nom' => 'Boissons 20%',
    ),
    29 => array(
        'type' => 3,
        'code' => '625700',
        'nom' => 'Repas (invitat° externe) 20%',
    ),
    30 => array(
        'type' => 3,
        'code' => '625700',
        'nom' => 'Repas (invitat° externe) 10%',
    ),
    31 => array(
        'type' => 3,
        'code' => '625600',
        'nom' => 'Repas (invitation interne)',
    ),
    32 => array(
        'type' => 3,
        'code' => '625600',
        'nom' => 'Repas individuel',
    ),
    33 => array(
        'type' => 3,
        'code' => '624800',
        'nom' => 'Taxi',
    ),
    34 => array(
        'type' => 3,
        'code' => '626000',
        'nom' => 'Téléphone / fax',
    ),
    35 => array(
        'type' => 3,
        'code' => '624800',
        'nom' => 'Train',
    ),
    36 => array(
        'type' => 3,
        'code' => '12345',
        'nom' => 'Taxi',
    ),
    37 => array(
        'type' => 3,
        'code' => '624800',
        'nom' => 'Transports urbains',
    ),
    38 => array(
        'type' => 3,
        'code' => '626000',
        'nom' => 'Frais Postaux',
    ),
    39 => array(
        'type' => 3,
        'code' => '606400',
        'nom' => 'Fournitures de Bureaux',
    ),
    40 => array(
        'type' => 3,
        'code' => '606311',
        'nom' => 'Tenues professionnelles',
    ),
    41 => array(
        'type' => 3,
        'code' => '606400',
        'nom' => 'Petit Matériel',
    ),
);