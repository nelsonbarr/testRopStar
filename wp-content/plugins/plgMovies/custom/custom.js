jQuery(document).ready(function($){
	// AL CAMBIAR EL SELECT DE GENERO
	$( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
		let genreId=$("#pa_generos").val()
		if(genreId!='nothing'){//SI GENERO DISTINTO DE NINGUNO
			let urlMoviesByGenre="https://api.themoviedb.org/3/discover/movie?api_key=c296a21bf0d332675ae61b5afc553ff5&with_genres="+genreId

			fetch(urlMoviesByGenre)
			.then(response => response.json())
			.then(data => {
				if($("#moviesContent" ).length<=0)//SINO EXISTE EL CONTENEDOR DE PELICULAS LO CREO
					$( ".product_custom_field" ).prepend('<div id="containerMovies"><h5>Seleccione pelicula</h5><div id="moviesContent" class="container"></div></div>')
				else
					$( "#moviesContent" ).html('')//SI EXISTE LO LIMPIO

				for(let i=0;i<9;i++) {//RECORRO EL ARREGLO DE PELICULAS HSATA MAXIMO 10 ITEMS
					$( "#moviesContent" ).append('<div class="item" onclick="javascript:selectMovie(\''+data.results[i].title+'\')"><img src="https://www.themoviedb.org/t/p/w138_and_h175_face/'+data.results[i].poster_path+'" ><div>'+data.results[i].title+'</div></div>')					
				}		
			})		
		}
		else{//SI GENERO NINGUNO, BLANQUEO EL CONTENEDOR DE PELICULAS, Y MARCO COMO PELICULA SELECCIONADA NINGUNA
			$("#containerMovies" ).html('')
			$("#_cstmProductMovie").val('Ninguna')
		}
		
	})
	
	

});

function selectMovie(title){//AGREGO EL TITULO SELECCIONADO AL CUSTOM FIELD DE PELICULA
	jQuery("#_cstmProductMovie").val(title)
}

