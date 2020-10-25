<?php

namespace App\Http\Controllers\Backend\Games;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\Backend\Games\GamesRepository;
use App\Http\Requests\Backend\Games\ManageGamesRequest;

/**
 * Class GamesTableController.
 */
class GamesTableController extends Controller
{
    protected $games;

    /**
     * @param \App\Repositories\Backend\Blogs\BlogsRepository $cmspages
     */
    public function __construct(GamesRepository $games)
    {
        $this->games = $games;
    }

    /**
     * @param \App\Http\Requests\Backend\Blogs\ManageBlogsRequest $request
     *
     * @return mixed
     */
    public function __invoke(ManageGamesRequest $request)
    {
        $data = $this->games->getForDataTable();

        return Datatables::of($data)
            ->escapeColumns(['name'])
            ->addColumn('id', function ($games) {
                return $games->id;
            })
            ->addColumn('title', function ($games) {
                return "<a href='http://canada777.com/game/".$games->name."' target='_blank'>".$games->title."</a>";
            })
            ->addColumn('tracking_details', function ($games) {

                $trackingDetails = 
                "<strong>Details:</strong>
                <textarea class='form-control' game_id='". $games->id ."'placeholder='Nothing was written.'>".htmlspecialchars($games->tracking_details) ."</textarea>
                ";

                $trackingDetails .= '<div class="tracking_actions">
                  <button type="button" class="btn btn-sm btn-primary edit-game-details" detail="tracking_details" game_id="'. $games->id .'">Edit Tracking Details</button>
                </div>';

                return 
                "<div class='tracking_details'>
                    $trackingDetails
                </div>";
            })
            ->addColumn('updated_at', function ($games) {
                return $games->updated_at->format('d/m/Y h:i A');
            })
            ->addColumn('actions', function ($games) {

                $isTracked = $games->tracking_status === 'Checked' ? "Checked" : "Unchecked";
                $isResponsive = $games->is_responsive === 'True' ? "True" : "False";
                $isWorking = $games->fn_status === 'True' ? "True" : "False";

                $btnColor1 = $games->tracking_status === 'Checked' ?  "btn-primary" : "btn-warning";
                $btnColor2 = $games->is_responsive === 'True' ?  "btn-success" : "btn-danger";
                $btnColor3 = $games->fn_status === 'True' ?  "btn-success" : "btn-danger";

                



                return 
                '
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm disabled btn-default">Tracking Status</button>
                  <button type="button" class="btn btn-sm '. $btnColor1 .' edit-game-details" detail="tracking_status" game_id="'. $games->id .'" value="'. $isTracked .'">'. $isTracked .'</button>
                </div>           
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm disabled btn-default">Game Is Mobile-Friendly?</button>
                  <button type="button" class="btn btn-sm '. $btnColor2 .' edit-game-details" detail="is_responsive" game_id="'. $games->id .'" value="'. $isResponsive .'">'. $isResponsive .'</button>
                </div>
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm disabled btn-default">Game is Working?</button>
                  <button type="button" class="btn btn-sm '. $btnColor3 .' edit-game-details" detail="fn_status" game_id="'. $games->id .'" value="'. $isWorking .'">'. $isWorking .'</button>
                </div>
                '
                ;
            })
            // ->addColumn('created_at', function ($games) {
            //     return $games->created_at->toDateString();
            // })
            ->make(true);
    }
}
