var API = new WebAppAPI();

//Page Index
API.addRoute('/','index.html',function( params, query, end )
{
	$.getJSON('../label/list',function(data)
	{
		end({"list":data,"add":"ajouterLabel()"});
	});
});

//Page View
API.addRoute('/view/:uid','view.html',function( params, query, end )
{
	$.getJSON('../label/get?uid='+params.uid,function(data)
	{
		end(data[0]);
	});
});

API.build();

