@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.games.management'))

@section('page-header')
    <h1>{{ trans('labels.backend.games.management') }}</h1>
@endsection

@section('content')
    <!-- HTML goes here -->

    <div id="games_panel" class="container-fluid">
        <div class="row">
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Tracked Games</h3>
                    <div class="totals">
                        <span class="tracked">...</span> out of <span class="total">...</span>
                    </div>
                </div>
           </div> 
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Needs SSL</h3>
                    <div class="totals">
                        <span class="ssl_problems">...</span> out of <span class="tracked_not_working">...</span>
                    </div>
                </div>
           </div> 
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Working Games</h3>
                    <div class="totals">
                        <span class="working_games">...</span> out of <span class="tracked">...</span>
                    </div>
                </div>
           </div> 
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Games that are not Working</h3>
                    <div class="totals">
                        <span class="tracked_not_working">...</span> out of <span class="tracked">...</span>
                    </div>
                </div>
           </div> 
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Non-existent Games</h3>
                    <div class="totals">
                        <span class="nonexistent_games">...</span> out of <span class="tracked_not_working">...</span>
                    </div>
                </div>
           </div> 
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Mobile-Friendly</h3>
                    <div class="totals">
                        <span class="mobile_friendly">...</span> out of <span class="working_games">...</span>
                    </div>
                </div>
           </div>
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Working, but not Mobile-Friendly</h3>
                    <div class="totals">
                        <span class="working_not_mobile_friendly">...</span> out of <span class="working_games">...</span>
                    </div>
                </div>
           </div>
           <div class="col-md-6 col-lg-3">
                <div class="well">
                    <h3>Games with Audio Issues</h3>
                    <div class="totals">
                        <span class="audio_problems">...</span> out of <span class="working_games">...</span>
                    </div>
                </div>
           </div>
        </div>
        <div class="box-body">
            <div class="table-responsive data-table-wrapper clipped-table">
                <table id="games-table" class="table table-condensed table-hover table-bordered">
                    <thead><tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Game Name
                        </th>
                        <th width="40%">
                            Tracking Details
                        </th>
                        <th>
                            Last Updated
                        </th>
                        <th width="24%">
                            Actions
                        </th>

                    </tr></thead>


                </table>

            </div>
        </div>
    </div>



            



   
@endsection

@section('after-scripts')
    {{-- For DataTables --}}
    {{ Html::script(mix('js/dataTable.js')) }}

    <script>

        dataTable = null;

        function displayTracker(){
            $.get('{{ route("admin.games.statistics") }}',function(results){
                $(".total").text(results.total);
                $(".tracked").text(results.tracked);
                $(".ssl_problems").text(results.ssl_problems);
                $(".display_problems").text(results.display_problems);
                $(".nonexistent_games").text(results.nonexistent_games);
                $(".mobile_friendly").text(results.mobile_friendly);
                $(".working_games").text(results.working_games);
                $(".working_not_mobile_friendly").text(results.working_not_mobile_friendly);
                $(".audio_problems").text(results.audio_problems);
                $(".tracked_not_working").text(results.tracked_not_working);   
                $(".pct").detach();             
                $("div.totals").each(function(){
                    num1 = parseInt($(this).find("span[class]").eq(0).text());
                    num2 = parseInt($(this).find("span[class]").eq(1).text());
                    pct = num1 / num2 * 100;
                    pct = pct.toFixed(1);
                    $(this).append("<span class='pct'> ("+pct+"%)</span>");
                });

            },"json");
        }

        function loadTable(){
            var dataTable = $('#games-table').dataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.games.get") }}',
                    type: 'post'
                },
                columns: [
                    {data: 'id', name: '{{config('module.games.table')}}.id'},
                    {data: 'title', name: '{{config('module.games.table')}}.title'},
                    {data: 'tracking_details', name: '{{config('module.games.table')}}.tracking_details'},
                    {data: 'updated_at', name: '{{config('module.games.table')}}.updated_at'},
                    {data: 'actions', name: 'actions', searchable: false, sortable: false, className: 'tracking_actions'}
                ],
                order: [[3, "asc"]],
                searchDelay: 500,
                //dom: 'lBfrtip',
                buttons: {
                    buttons: [
                    ]
                },
                language: {
                    @lang('datatable.strings')
                }
            });
        }

        function updateGame(id,detail,value){
            isAValidColumn = true;
            switch(detail){
                case "tracking_status":
                    value = value === 'Unchecked' ? "Checked" : "Unchecked";
                break;
                case "is_responsive":
                case "fn_status":
                    value = value === 'False' ? "True" : "False";
                break;
                case "tracking_details":
                break;
                default:
                    isAValidColumn = false;
                break;
            }
            if(isAValidColumn){
                $.post('{{ route("admin.games.update_game") }}',{detail: detail,value: value,game_id: id},function(results){
                    switch(results.detail){
                        case "fn_status":
                        case "is_responsive":
                            newText = $("[game_id='"+results.game_id+"'][detail='"+results.detail+"']").text() === "True" ? "False"
 : "True";
                            $("[game_id='"+results.game_id+"'][detail='"+results.detail+"']").toggleClass("btn-danger btn-success").text(newText).attr("value",newText);
                            displayTracker();
                        break;
                        case "tracking_status":
                            newText = $("[game_id='"+results.game_id+"'][detail='"+results.detail+"']").text() === "Checked" ? "Unchecked"
 : "Checked";
                            $("[game_id='"+results.game_id+"'][detail='"+results.detail+"']").toggleClass("btn-primary btn-warning").text(newText).attr("value",newText);
                            displayTracker();
                        break;
                        case "tracking_details":
                            displayTracker();
                        break;
                    }
                    
                },"json");
            }

        }

        $(function() {

            $("body").on("click",".edit-game-details",function(event){
                event.preventDefault();
                id = $(this).attr("game_id");
                detail = $(this).attr('detail');
                value = "";
                switch(detail){
                    case "fn_status":
                    case "is_responsive":
                    case "tracking_status":
                        value = $(this).attr('value');
                    break;
                    case "tracking_details":
                        value = $(this).parents(".tracking_actions").prev("textarea[game_id]").val();
                    break;
                }
                updateGame(id,detail,value);
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            displayTracker();

            loadTable();

            Backend.DataTableSearch.init(dataTable);
        });
    </script>
    <style type="text/css">

        div.totals {
            font-size: 130%;
        }

        #tracked,#total {
            font-weight: bold;
            font-size:120%;
        }
        .tracked{color:#f90;}
        .total{color:#08c}
        td.tracking_actions .btn-group {
            /* position: static; */
            /* float:left; */
            display: block;
            margin-bottom:3px;
        }

        td.tracking_actions .btn-group::after {content: "";
         clear: both;
         display: block;
         height: 0;
         visibility: hidden;}

        td.tracking_actions .btn.disabled:hover,td.tracking_actions .btn.disabled:focus{
            background:transparent!important;
            outline:0;
            cursor:default;
        }

        .btn-group {}

        .tracking_actions .btn-sm {
            font-size: 11px;
            padding: 3px 7px;
        }

        div#games-table_wrapper .row .col-sm-12 {
            max-height: 400px;
            overflow: auto;
        }

        .tracking_details textarea.form-control{width:100%;display:block;margin-bottom:4px;resize:none;}

        #games_panel .well {
            background: #333;
            color: #fff;
            border: 2px solid #000;
            text-shadow: 0 2px 0 #000;
        }

        .well h3 {
            font-weight: bold;
        }

        span.ssl_problems {color: #e4ff00;}

        span.nonexistent_games {
            color: #f00;
        }

        span.mobile_friendly {
            color: #7bd47b;
        }

        span.working_games {
            color: #0f0;
            border-bottom: 2px dotted #0f0;
        }

        span.tracked_not_working {
            color: #f50;
            border-bottom: 6px double #f50;
        }

        span.working_not_mobile_friendly {
            color: #a0a0a0;
        }

        span.audio_problems {
            color: #d876d8;
        }

        </style>
@endsection