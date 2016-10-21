@extends('layouts.app')

@section('gameScript')
    <script src="{{ URL::asset('js/statistics.js') }}"></script>
    <script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script>
@endsection

@section('content')
    <div class="container">
        <div class="row" style="margin-bottom: 5px;">
            <input type="hidden" name="lastDateValue" value="">
            <input type="hidden" name="nextDateValue" value="">
            <div class="btn btn-warning" id="showAll">全部</div>
            <div class="btn btn-success" id="today">本日</div>
            <div class="btn bg-default">//</div>
            <div class="btn btn-primary " id="lastDate">前一日</div>
            <div class="inputGroup">
                <input type="text" name="userChangeDate" value="" maxlength="8" size="10">
                <div class="btn btn-info" id="userChange">查詢</div>
            </div>
            <div class="btn btn-primary" id="nextDate">後一日</div>
            <div class="btn bg-default">//</div>
            <div class="btn bg-danger">
                開始日
                <select id="startDate" name="startDate"></select>
                期數
                <select id="startGamesNo" name="startGamesNo"></select>
            </div>
            <div class="btn bg-default">
                ~
            </div>
            <div class="btn bg-danger">
                結束日
                <select id="endDate" name="endDate"></select>
                期數
                <select id="endGamesNo" name="endGamesNo"></select>
            </div>
            <div class="btn btn-info" id="blockSearch">查詢</div>
        </div>
        <div class="row" style="margin-top: 10px; border: 1px solid darkblue; border-radius: 4px; overflow: hidden;">
            <div class="col-xs-2" style="padding-top: 5px; padding-bottom: 5px; text-align: center; background: #337ab7; color: white">日期區間</div>
            <div class="col-xs-10" style="padding-top: 5px; padding-bottom: 5px;">
                <span id="date_min"></span>&nbsp;&nbsp;~&nbsp;&nbsp;<span id="date_max"></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px; border: 1px solid darkblue; border-radius: 4px; overflow: hidden;">
            <div class="col-xs-2" style="padding-top: 5px; padding-bottom: 5px; text-align: center; background: #337ab7; color: white">期數區間</div>
            <div class="col-xs-10" style="padding-top: 5px; padding-bottom: 5px;" >
                <span id="no_min"></span>&nbsp;&nbsp;~&nbsp;&nbsp;<span id="no_max"></span>
            </div>
        </div>
        <div class="row" style="margin-top: 10px; border: 1px solid darkblue; border-radius: 4px; overflow: hidden;">
            <div class="col-xs-2" style="padding-top: 5px; padding-bottom: 5px; text-align: center; background: #337ab7; color: white">統計期數</div>
            <div class="col-xs-10" style="padding-top: 5px; padding-bottom: 5px;" >
                <span id="no_total"></span>&nbsp;&nbsp;期
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="btn btn-default">出現次數</div>
            <div class="btn btn-info">最少</div>
            <div class="btn bg-success">平均</div>
            <div class="btn btn-danger">最多</div>
        </div>

        <div class="row" style="margin-top: 5px;" id="digitalContainer"></div>
        <div class="row" style="margin-top: 20px;" id="chartContainer"></div>
    </div>
@endsection