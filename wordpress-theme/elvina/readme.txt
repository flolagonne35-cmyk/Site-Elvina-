=== Elvina ===
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
WC requires at least: 8.0
WC tested up to: 9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Theme sur mesure pour la boutique Elvina (mode & bijoux), avec support
WooCommerce natif. Voir wordpress-theme/README-INSTALLATION.md a la racine
du depot pour la marche a suivre complete (installation, import du
catalogue, prise en main de la caisse comptoir).

== Description ==

* Page d'accueil personnalisee (hero, tuiles Mode/Bijoux, selection du
  moment).
* Boutique WooCommerce standard (fiches produit, panier, commande) stylee
  aux couleurs de la marque.
* Etiquettes de stock ("Derniere piece", "Rupture de stock", "Stock
  limite") calculees a partir du stock reel de chaque variante (taille /
  finition), pour ne jamais laisser un article vendu apparaitre encore
  disponible.
* Caisse comptoir (WooCommerce > Caisse comptoir) : ecran admin pour
  enregistrer une vente en boutique. Cree une vraie commande WooCommerce
  et decremente le meme stock que le site, sans gerer de paiement carte
  (le terminal de paiement reste separe).
* Formulaire de contact natif (sans extension), via wp_mail().

== Changelog ==

= 1.1.0 =
* Ajout de la caisse comptoir (WooCommerce > Caisse comptoir).

= 1.0.0 =
* Version initiale, portee depuis le prototype statique.
