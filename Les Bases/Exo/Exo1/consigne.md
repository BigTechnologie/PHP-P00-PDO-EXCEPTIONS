# Exercice 1

## 1

Tous les attributs utilisés doivent être définis comme protected.

Créer 3 classes

Mage
Guerrier   
Voleur

Chacune des classes doit avoir les attributs suivants

Nom $nom;
Points de vie $pdv;
Points d'attaque $pa;

Cependant, vous ne pouvez pas définir d'attributs dans les classes Mage Guerrier et Voleur, il faut utiliser l'héritage

Le Mage doit avoir une méthode "boule de feu", le guerrier une méthode "charge", et le voleur une méthode "vol à la tire" qui affichent toutes les trois des textes différents.

Chacune de ses classes doit avoir une fonction attaque($cible) qui retire x points de vie à la cible, x étant la valeur de l'attribut attaque de l'objet en question et affiche ('Nom de l'objet 1' attaque 'Nom de l'objet 2' avec x Points de dégats)

Bonus : si les points de vie d'un objet sont nul ou négatifs après l'attaque d'un autre objet, celui-ci doit afficher une phrase du type : 'nom de l'objet' est mort.

Tester le resultat dans un fichier: index.php