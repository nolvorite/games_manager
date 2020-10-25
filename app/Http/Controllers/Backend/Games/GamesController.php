<?php

namespace App\Http\Controllers\Backend\Games;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogsController.
 */
class GamesController extends Controller
{

	public function __construct(){
		$this->response = ['status' => false, 'data' => [], 'errors' => ''];
	}

	public $response;

	public function index(){
		return view('backend.games.index');
	}

	public function statistics(){
		$data = (array) DB::table('w_games')->select(DB::raw('count(distinct name) as total'))->orderBy('updated_at')->get()->first();
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as tracked'))->where("tracking_status", 'Checked')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as ssl_problems'))->where("tracking_details", 'LIKE', '%SSL%')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as display_problems'))->where("tracking_details", 'LIKE', '%text needs to be%')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as nonexistent_games'))->where("tracking_details", 'LIKE', '%exist%')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as mobile_friendly'))->where("is_responsive", 'True')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as working_games'))->where("fn_status", '=', 'True')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as tracked_not_working'))->where("tracking_status", '=', 'Checked')->where("fn_status", 'False')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as working_not_mobile_friendly'))->where("fn_status", '=', 'True')->where("is_responsive", 'False')->orderBy('updated_at')->get()->first());
		$data = array_merge($data, (array) DB::table('w_games')->select(DB::raw('count(distinct name) as audio_problems'))->where("tracking_details", 'LIKE', '%audio%')->orWhere("tracking_details", 'LIKE', '%sound%')->orderBy('updated_at')->get()->first());
		$data['untracked'] = $data['total'] - $data['tracked'];

		return Response()->json($data);
	}

	public function update_game(Request $request){

		$data = $this->response;
		$data['message'] = '';

		if($request->has('detail') && $request->has('game_id') && $request->has('value')){
			//search for the game to reference
			$gameId = intval($request->get('game_id'));
			$gameSearch = (array) DB::table('w_games')->select(DB::raw('*'))->where("id",$gameId)->orderBy('updated_at')->get()->first();
			$valueToUpdateWith = "";
			$isAValidColumn = true;
			if(gettype($gameSearch) === 'array'){
				switch($request->get('detail')){
					case 'tracking_status':
						$valueToUpdateWith = $request->get('value') === 'Checked' ? "Checked" : "Unchecked";
					break;
					case 'is_responsive':
					case 'fn_status':				
						$valueToUpdateWith = $request->get('value') === 'True' ? "True" : "False";
					break;
					case "tracking_details":
						$valueToUpdateWith = $request->get('value');
					break;
					default:
						$isAValidColumn = false;
					break;
				}
				if($isAValidColumn){
					DB::table('w_games')->where("id",$gameId)->update([
						$request->get('detail') => $request->get('value'),
						'updated_at' => DB::Raw('NOW()')
					]);
					$data['status'] = true;
					$data['detail'] = $request->get('detail');
					$data['game_id'] = $request->get('game_id');
					$data['message'] = "Successfully modified column " .$request->get('detail');
				}else{
					$data['message'] = "Error: Invalid column to modify.";
				}
				
			}else{
				$data['message'] = "Error: The game you are trying to modify was deleted, or no longer exists.";
			}
		}else{
			$data['message'] = "Error: Invalid number of parameters.";
		}
		return Response()->json($data);
	}


}