// Catalogue Elvina — source unique du stock (voir guide "un seul endroit maître").
// En production, ce tableau sera remplacé par les données de votre solution
// caisse + stock (Square, WooCommerce...) via son API.

var PRODUCTS = [
  {
    id: "robe-satin-ivoire",
    category: "mode",
    name: "Robe Satin Ivoire",
    price: 89,
    description: "Robe longueur midi en satin fluide, coupe cintrée et dos drapé. Une pièce qui passe du comptoir de la boutique à une soirée sans effort.",
    variantLabel: "Taille",
    variants: [
      { label: "XS", stock: 2 },
      { label: "S", stock: 4 },
      { label: "M", stock: 1 },
      { label: "L", stock: 0 }
    ]
  },
  {
    id: "chemise-lin-sable",
    category: "mode",
    name: "Chemise Lin Sable",
    price: 59,
    description: "Chemise en lin lavé, coupe ample, col mao. Se porte ouverte sur un total look ou boutonnée pour le bureau.",
    variantLabel: "Taille",
    variants: [
      { label: "S", stock: 3 },
      { label: "M", stock: 3 },
      { label: "L", stock: 3 },
      { label: "XL", stock: 2 }
    ]
  },
  {
    id: "pantalon-tailleur-anthracite",
    category: "mode",
    name: "Pantalon Tailleur Anthracite",
    price: 75,
    description: "Pantalon droit taille haute, tissu structuré à fine maille. Le compagnon de la chemise en lin les jours de rendez-vous.",
    variantLabel: "Taille",
    variants: [
      { label: "36", stock: 1 },
      { label: "38", stock: 2 },
      { label: "40", stock: 2 },
      { label: "42", stock: 0 }
    ]
  },
  {
    id: "trench-leger-camel",
    category: "mode",
    name: "Trench Léger Camel",
    price: 129,
    description: "Trench mi-saison en gabardine légère, ceinture amovible. Une pièce arrivée en petite quantité.",
    variantLabel: "Taille",
    variants: [
      { label: "S", stock: 1 },
      { label: "M", stock: 0 },
      { label: "L", stock: 2 }
    ]
  },
  {
    id: "collier-fil-or-fin",
    category: "bijoux",
    name: "Collier Fil d'Or Fin",
    price: 39,
    description: "Chaîne fine à porter seule ou en superposition, fermoir mousqueton. Plaqué or.",
    variantLabel: "Finition",
    variants: [
      { label: "Doré", stock: 5 },
      { label: "Argenté", stock: 3 }
    ]
  },
  {
    id: "boucles-perle-nacree",
    category: "bijoux",
    name: "Boucles d'Oreilles Perle Nacrée",
    price: 29,
    description: "Puces épurées serties d'une perle nacrée. Le petit détail qui change un visage.",
    variantLabel: "Finition",
    variants: [
      { label: "Doré", stock: 4 },
      { label: "Argenté", stock: 4 }
    ]
  },
  {
    id: "bracelet-maille-milanaise",
    category: "bijoux",
    name: "Bracelet Maille Milanaise",
    price: 45,
    description: "Bracelet souple à maille serrée, fermoir aimanté. Un classique qui ne quitte plus le poignet.",
    variantLabel: "Finition",
    variants: [
      { label: "Doré", stock: 2 },
      { label: "Argenté", stock: 1 }
    ]
  },
  {
    id: "bague-solitaire-zircon",
    category: "bijoux",
    name: "Bague Solitaire Zircon",
    price: 35,
    description: "Bague fine sertie d'un zircon rond, anneau ajustable. Discrète le jour, brillante le soir.",
    variantLabel: "Taille",
    variants: [
      { label: "50", stock: 1 },
      { label: "52", stock: 2 },
      { label: "54", stock: 0 },
      { label: "56", stock: 3 }
    ]
  }
];

function formatPrice(value) {
  return value.toLocaleString("fr-FR", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " €";
}

function findProduct(id) {
  return PRODUCTS.find(function (p) { return p.id === id; });
}

function totalStock(product) {
  return product.variants.reduce(function (sum, v) { return sum + v.stock; }, 0);
}
