function render( file, data )
{
	return new Promise(function( resolve, reject)
	{
		$.get({
		  url: file, 
		  cache: false
		}).then(function(tpl){
		  resolve( Mustache.render(tpl, data) );
		});
	});
}



var WebAppAPI_open = '';
class WebAppAPI
{
	constructor()
	{
		//array route
		this.tabRoute = [];

		// initialize the application
		var root = null;
		var useHash = true; // Defaults to: false
		var hash = '#!'; // Defaults to: '#'
		this.router = new Navigo(root, useHash, hash);
	}

	//Ajouter une route
	addRoute( route, template, callback)
	{
		this.tabRoute.push({
			'route':route,
			'file':template,
			'callback':callback,
			'params':[],
			'query':[],
			'fonc':function( route, params, query )
			{
				var end = function( rep )
				{
					render('template/page/'+route.file, rep)
					.then(function( tpl )
					{
						$('main').html(tpl);
					});
				};

				if(route.callback)
					route.callback( params, query, end );
				else
					end([]);
			}
		});
	}



	//Build APP
	build()
	{
		let tabRoute = this.tabRoute;
		let router = this.router;
		$(document).ready(function()
		{
			console.log('WebAppAPI: Jquery OK');
			render('template/main.html')
			.then(function( tpl )
			{
				console.log('WebAppAPI: Main Template OK');
				$('body').html(tpl);

				var fonc = function( route )
				{
					router.on(route.route, function( params, query )
					{
						console.log('WebAppAPI: Load page: '+route.route);

						route.params = params;
						route.query  = query;
						route.fonc( route, params, query );
						WebAppAPI_open = {'route':route, 'params':params, 'query':query};
					});
				};

				//Création des routes
				for( var i=0; i<tabRoute.length; i++)
					fonc( tabRoute[i] );

				//Activer route
				router.resolve();
			});
		});
	}


	//Refresh la page
	refresh()
	{
		console.log('WebAppAPI: Reload page: '+WebAppAPI_open.route.route);
		WebAppAPI_open.route.fonc( 
			WebAppAPI_open.route, 
			WebAppAPI_open.params, 
			WebAppAPI_open.query );
	}


	//Charger une route
	load( $route )
	{
		console.log('WebAppAPI: change page: '+WebAppAPI_open.route.route);
		this.router.navigate( $route );
	}
}