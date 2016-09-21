<?php

return [

    /**
     * 每回合可下注時間（秒）
     */
    'TIME_OF_ROUND' => 60,

    /**
     * 每期遊戲回合數
     */
    'ROUND_PER_GAME' => 3,

    /**
     * 每期遊戲間隔
     */
    'GAME_INTERVAL' => 10,

    /**
     * 每回合遊戲間隔
     */
    'ROUND_INTERVAL' => 10,

    /**
     * 選號範圍（最小值）
     */
    'CODE_RANGE_MIN' => 1,

    /**
     * 選號範圍（最大值）
     */
    'CODE_RANGE_MAX' => 40,

    /**
     * 標準賠率
     */
    'STANDARD_ODDS' => 0.92,

    /**
     * 遊戲狀態預設值：執行中
     */
    'STATE_RUNNING' => 0,

    /**
     * 遊戲狀態預設值：結算中
     */
    'STATE_CLOSING' => 1,

    /**
     * 遊戲狀態預設值：已結束
     */
    'STATE_CLOSED' => 2,
];