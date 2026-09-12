# Installer Elvina sur WooCommerce

Ce dossier contient un vrai thème WordPress (`elvina/`) et un fichier
d'import (`elvina-products-import.csv`) pour rejouer le catalogue du
prototype statique dans WooCommerce — la vraie base qui pourra être
partagée entre le site et une caisse physique.

**Important à savoir avant de commencer** : ce thème a été relu et
vérifié syntaxiquement (`php -l` sur chaque fichier), mais je n'ai pas pu
l'installer sur un vrai WordPress pour le tester en conditions réelles
dans cette session — l'accès à wordpress.org est bloqué par la politique
réseau de cet environnement. Une fois installé chez vous, testez au
minimum : la page d'accueil, la boutique, une fiche produit avec choix de
variante, l'ajout au panier et la commande.

## 1. Pré-requis : un hébergement WordPress

Si vous n'avez pas encore d'hébergement, cherchez une offre avec
"installation WordPress en 1 clic" (o2switch, Hostinger, LWS, PlanetHoster
et d'autres en proposent) — comptez environ 5 à 10 €/mois, ce qui laisse
de la marge sous votre budget de 30 €/mois pour la suite. Vérifiez que
PHP 7.4+ est disponible (c'est le cas par défaut chez la quasi-totalité
des hébergeurs actuels).

## 2. Installer WordPress et WooCommerce

1. Installez WordPress via l'outil "1 clic" de votre hébergeur (ou
   manuellement si vous préférez).
2. Dans l'admin WordPress : **Extensions > Ajouter** → cherchez
   "WooCommerce" → Installer → Activer.
3. Laissez WooCommerce créer ses pages automatiquement (Boutique, Panier,
   Commande, Mon compte) pendant son assistant de configuration — vous
   pouvez passer les étapes de paiement pour l'instant, on y reviendra.

## 3. Installer le thème Elvina

1. Compressez le dossier `elvina/` en `elvina.zip` (le zip doit contenir
   directement `style.css`, `functions.php`, etc. — pas un sous-dossier
   supplémentaire).
2. Dans l'admin WordPress : **Apparence > Thèmes > Ajouter > Téléverser un
   thème** → sélectionnez `elvina.zip` → Installer → Activer.

## 4. Créer les pages "À propos" et "Contact"

Le thème affiche automatiquement son design personnalisé sur une page dès
que son **slug** (l'identifiant dans l'URL) correspond :

1. **Pages > Ajouter** → Titre "À propos" → vérifiez que le permalien
   (sous le titre) est bien `a-propos` → Publier.
2. **Pages > Ajouter** → Titre "Contact" → vérifiez que le permalien est
   bien `contact` → Publier.

Le contenu de ces pages n'a pas besoin d'être rempli : le thème affiche
son propre contenu pour ces deux pages précises.

## 5. Créer les catégories de produits

**Produits > Catégories** → créez deux catégories avec exactement ces
noms (le slug se génère tout seul) :
- `Mode`
- `Bijoux`

## 6. Importer le catalogue

1. **Produits > Tout > Importer**.
2. Choisissez le fichier `elvina-products-import.csv`.
3. À l'étape de correspondance des colonnes, WooCommerce devrait
   reconnaître automatiquement chaque colonne (elles portent les noms
   standard de son propre export). Vérifiez juste que "Categories" pointe
   bien vers vos catégories Mode/Bijoux créées à l'étape 5.
4. Lancez l'import : vous obtenez les 8 articles avec leurs tailles ou
   finitions, et le stock réel de chacune.

Les produits importent **sans photo** (le prototype n'en avait pas non
plus — juste des aplats de couleur). Ajoutez de vraies photos ensuite
dans **Produits > [nom du produit] > Image mise en avant / Galerie**.

## 7. Choisir une extension caisse (le "logiciel de caisse")

C'est la pièce qui manquait dans le prototype statique. Dans l'admin :
**Extensions > Ajouter**, cherchez "point of sale" ou "POS for
WooCommerce", et comparez les options actuelles (notes, nombre
d'installations, date de dernière mise à jour) — je préfère ne pas vous
recommander un nom précis sans pouvoir vérifier son état aujourd'hui,
les extensions évoluent vite. Ce que vous cherchez : une extension qui
tourne **à l'intérieur de ce même WooCommerce** (pas un service tiers
séparé), pour que la caisse et le site continuent de lire le même stock
que celui importé à l'étape 6.

Si aucune ne convient, l'alternative reste Square ou SumUp en caisse
séparée, avec leur connecteur WooCommerce officiel.

## 8. Activer un vrai moyen de paiement

**WooCommerce > Réglages > Paiements** → activez au minimum un moyen
(virement, Stripe, PayPal...) pour que le bouton "Commander" encaisse
réellement — sans ça, WooCommerce accepte les commandes mais aucun
paiement ne circule.

## Ce qui reste à faire ensuite

- Ajouter de vraies photos produits.
- Choisir et configurer l'extension caisse (étape 7).
- Activer un moyen de paiement réel (étape 8).
- Refaire les deux tests du premier guide une fois tout branché : vendre
  un article en caisse et vérifier qu'il disparaît du site, puis
  commander en ligne et vérifier que le stock caisse bouge.
