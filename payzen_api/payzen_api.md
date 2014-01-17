##Payzen API prototype using PHP/Laravel

###Requirements :
PHP 5.4+ (laravel 5.3, payzen code 5.4)

Mandatory extensions : mcrypt (laravel), soap (TODO)

Recommended extensions : memcached (TODO for generating trans_id)

Also see composer.json


---

###What's nice...
####...about PHP :
* very popular => huge community & support, installed by default on most web providers, extensions, open-source projects (prestashop, magento...)
* our clients use it, our support team too (and we already have code for form generation, WS...)
* excellent documentation (in french too)
* powerful native features for templating, escaping, input validation, regexes, string/json/array manipulation...
* evolves quite quickly (since 2009 : removed magic quotes, added traits, closures, chained calls, namespaces, yield...)
* fast, low memory consumption, no compilation, no server restart, copy your code to apache and you're done

####...about Laravel :
* based on symfony, one of the main "enterprisey" php frameworks
* built with composer, the main php dependency manager
* good documentation (whole phpdoc + good presentation of main features)
* active community, with lots of pluggable bundles (ide helper generator, scaffolding generator, curl lib...)
* php 5.3+ => uses recent powerful PHP features (late static binding, closures, etc.) for much lighter code
* rails-like (light & powerful syntax based on conventions). App and code structure looks very much like Greg's grape
* ReSTful to the bone :

<pre>
Route::resource('charges', 'ChargesController');
/* Maps in a single line :
  Verb		  Path					          Action		Internal route name   note
	GET 		  /charge 				        index 		charge.index          list all charges
	GET 		  /charge/create 			    create 		charge.create         creation form
	POST 		  /charge 				        store 		charge.store          creation action
	GET 		  /charge/{charge} 		    show 		  charge.show           details one item
	GET 		  /charge/{charge}/edit   edit 		  charge.edit           update/delete form
	PUT/PATCH /charge/{charge} 		    update 		charge.update         update action
	DELETE 		/charge/{charge} 		    destroy 	charge.destroy        delete action
*/
</pre>

---

###What you may or may not like...
####...about PHP :
* syntax (ligther than java, more familiar than ruby, but cumbersome OOP syntax) : $object->method( Namespace\Class::staticMethod( ['key'=>'value'] );
* dynamic typing (although less frightening than javascript)
* liberty : lots of frameworks, 2 debuggers, 4 ways to define a string, hundreds of way to do things (most can go wrong)
* eclipse PHP tools are ok, but not great (lacks refactoring, limited auto-completion) but some other IDEs perform better (aptana which is eclipse-based, phpStorm...)

####...about Laravel :
* may become difficult to maintan if developers don't agree on conventions
* inversion of controller

---

###What is bad...
####... about PHP :
* highly depends on local configuration (huge constraints for "universal" deployment)
* inconsistencies in basic API (function names, parameters order...)
* dangerous features for unexperienced developers, especially in older versions ("magic quotes", "extract" function...)
* still no native Unicode support (encode your source in UTF-8 and you'll be find though)

####...about Laravel :
* 5.3+ => cannot be installed everywhere

---

###Greg's list :
* parsing json : Input::isJson() / Input::json()
* generating json : Input::wantsJson() / $entity->toJson()
* persistence :

<pre>
$charge = Charge::create( ['attribute' => 'value'...] );
$charge->contexts()->save(new Context( ['attribute' => 'value'...] ));
</pre>
* validation :

<pre>
//FIXME Laravel validator cannot validate sub-arrays (available_methods, etc.)
$validation = Validator::make($params, [
  "amount" => "required|numeric|min:0.00001",
  "currency" => "required|alphanum|size:3"]
);
if($validation->fails()) {
  App::abort(400, $validation->errors());
}

// native PHP alternative
$amount = filter_var( $params['amount'], FILTER_VALIDATE_INT, $options);
if( $amount === false ) {
  // bad amount !
}
</pre>

* Client soap : extension PHP Soap à installer/configurer (de base dans xampp). Manu connaît mieux que moi (pour l'instant...)

<pre>
// in $option we can map xml elements to custom PHP classes (not tested)
$client = new SoapClient( $wsdlURL, $options );
$object = $client->__soapCall( 'getInfo', $array );
</pre>

* Client rest : extension PHP Curl (avec un bundle Laravel pour simplifier la syntaxe).

<pre>
$curl = new Curl();
$curl->ssl( Config::get( "payzenapi.ssl_verifypeer" ) );
$html = $curl->simple_post( Config::get( "payzenapi.form_url" ), $data );
$headers = $curl->response_headers;
$cookies = $curl->response_cookies; // Home-made !
</pre>

* Gestion des exceptions : similaire à java (try, catch, throw new CustomException...). Laravel catche & loggue toutes les exceptions par défaut
