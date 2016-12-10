var rowHTML  = "<div class='row'></div>";
var cellHTML = "<div class='cell'></div>";


function toggleGrid(){
	$('.grid').toggleClass('hide');
}

var Game = null;
$( function () {

	// var grid = [
	// 	[null, null, null],
	// 	[null, null, null],
	// 	[null, null, null],
	// ];
	
	var gameStarted = false;
	
	function createGrid(grid){
		var cellId = 0;
		grid.forEach( function ( row ) {
			var rowElem = $( rowHTML );
			$( '.grid' ).append( rowElem );
			row.forEach( function ( cell, index ) {
				var cellObject = new Cell( cell ,cellId++ );
				rowElem.append( cellObject.element );
			} )
		} )
	}
	
	function Cell( value, id ) {
		var element = $( cellHTML );
		element.attr( 'id', 'cell-' + id );
		element.html( value );
		
		element.click( function () {
			var elem = $(this);
			var data = {
				i: parseInt(id / 3),
				j: parseInt(id % 3)
			};
			$.post('addSign',data,function(resp){
				if( resp ){
					elem.text( resp['sign'] );
					if( resp['winner'] == "1" ){
						$('.win').removeClass('hide');
						clearInterval(updater);
					}
				}
			});
		} );
		
		this.element = element;
	}
	
	function update(){
			$.get('/update',function(resp){
				if( resp['winner'] == "1" ){
					$('.win').removeClass('hide');
					clearInterval(updater);
				}
				if( resp['winner'] == "0" ){
					$('.lose').removeClass('hide');
					clearInterval(updater);
				}
				
				Game = resp;
				if( resp['user2_id'] && !gameStarted  ){
					gameStarted = true;
					$('.loading').addClass('hide');
					$('.grid').removeClass('hide');
					
					createGrid(Game.table_data);
				}
				
				var cellId = 0;
				Game.table_data.forEach(function(row){
					row.forEach(function(cell , index){
						$('#cell-'+cellId).html(cell);
						cellId++;
					})
				})
				
			});
	}
	
	$.get('/play',function(resp){
		data = resp;
		if( data.constructor == Array ){
			// begin game
			// console.log("begin game ->",data);
			toggleGrid();
			createGrid(data);
			$('.loading').addClass('hide');
			gameStarted = true;
		}else{
			// wait for player
			console.log("wait for player ->");
		}
		
	});
	
	var updater = setInterval(function(){
		update();
	},500);
	
	
	
} );

