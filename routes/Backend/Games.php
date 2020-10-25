<?php

Route::group(['namespace' => 'Games'], function(){
	Route::resource('games', 'GamesController');

	Route::post('games/get', 'GamesTableController')
   ->name('games.get');

   Route::get('game_statistics', 'GamesController@statistics')
   ->name('games.statistics');

    Route::post('update_game', 'GamesController@update_game')
   ->name('games.update_game');

});

?>