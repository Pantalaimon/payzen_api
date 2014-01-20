##Prototype d'API pour Payzen en PHP/Laravel

###Requirements :
PHP 5.4+ (laravel 5.3, code appli 5.4)

Extensions PHP obligatoires : mcrypt (laravel), soap (pour les WS, pas encore implémenté)

Extensions PHP recommandées : memcached (pour une meilleure génération du trans_id, pas encore implémenté)

Voir composer.json pour les librairies php


---

###Ce qui est bien...
####...avec PHP :
* Extrêmement populaire => grosse communauté, beaucoup de bibliothèques, projets open source (prestashop, magento...), sources de support etc.
* Fourni par défaut chez la quasi-totalité des hébergeurs
* Utilisé par nos clients et notre équipe support (possibilité de reprendre le code pour le formulaire de paiement et les WS)
* Excellente documentation, y compris en français
* Conçu pour le web avec de puissantes fonctionnalités natives : templates, validation/échappement des paramètres, urls et html, traitement du texte...
* Évolution rapide (depuis 2009 : ajout des traits, fermetures, appels chaînés, espaces de nom, mot clé "yield"...)
* Rapide, consomme très peu de mémoire, pas de compilation ni de redémarrage du serveur

####...avec Laravel :
* Dérivé de symphony, assemblé avec composer, outils de référence en PHP
* Bonne documentation (phpdoc + guide des principales fonctionnalités + code et configuration bien commentés...)
* Communauté active, pas mal de plugins
* L'outil en ligne de commande pour la génération de code, la migration bdd...
* Très inspiré de rails : conventions > configuration, syntaxe puissante, système de migrations bdd, conçu pour ReST :

<pre>
Route::resource('charges', 'ChargesController');
/* Déclare en une ligne :
Verbe		Chemin					Action		Nom de route		note
GET			/charge					index		charge.index		list all charges
GET			/charge/create			create		charge.create		creation form
POST		/charge					store		charge.store		creation action
GET			/charge/{charge}		show		charge.show			details one item
GET			/charge/{charge}/edit	edit		charge.edit			update/delete form
PUT/PATCH	/charge/{charge}		update		charge.update		update action
DELETE 		/charge/{charge}		destroy		charge.destroy		delete action
*/
</pre>

---

###Ce qu'on aime ou pas...
####...avec PHP :
* La syntaxe (ressemble au C avec des "$" partout et des opérateurs de POO un peu lourds)
* Types et noms de variable dynamiques, chargement de classes et méthodes "magiques" => code très léger mais auto-complétion et refactoring limités sous eclipse (à voir avec des IDEs plus spécialisés).
* La liberté : beaucoup de frameworks concurrents, de conventions de codage, 2 débuggeurs...

####...avec Laravel :
* Malgré les conventions, beaucoup de liberté dans l'organisation du code
* Les routes, filtres, évènements et l'inversion de contrôle qui permettent d'éclater le code et de changer le comportement par simple configuration
* Framework généraliste, il existe peut-être mieux pour créer une API json

---

###Ce qui est dommage...
####...avec PHP :
* Dépend beaucoup de la configuration locale (version, extensions, etc.)
* Inconsistances historiques dans certaines API de base
* Support unicode incomplet

####...avec Laravel :
* PHP 5.3 + extensions => pas garanti qu'il puisse être installé partout
* Moins bon pour certains cas aux limites (validation des sous-tableaux json par exemple)
* Url "http://localhost/monAppli**/public/**" par défaut

---

###Greg's list :
* parsing json :
<pre>
$params = Input::isJson() ? Input::json() : Input::all();
</pre>

* generation json :

<pre>return Input::wantsJson()
	? $entity->toJson()
	: View::make('entity.show', ['entity'=>$entity]);
</pre>

* persistence :

<pre>
$charge = Charge::create( ['attribute' => 'value'...] );
$charge->contexts()->save(new Context( ['attribute' => 'value'...] ));
</pre>
* validation : Laravel fournit un "Validator" sympa mais limité, PHP fournit des filtres natifs (notamment pour les emails, ip, url...).

<pre>
$validation = Validator::make($params, [
  "amount" => "required|numeric|min:0.00001",
  "currency" => "required|alphanum|size:3"
]);
if($validation->fails()) {
  App::abort(400, $validation->errors());
}
</pre>

* Client soap : extension PHP Soap (installée de base dans xampp).

<pre>
$client = new SoapClient( $wsdlURL, $options );
$object = $client->__soapCall( 'getInfo', $array );
</pre>

* Client rest : extension PHP Curl (avec un plugin Laravel pour alléger la syntaxe).
* Gestion des exceptions : similaire à java. Laravel fournit un système de ExceptionHandler pour intercepter et traiter tout type d'exception (celui par défaut loggue tout avec une stacktrace).
