<?php

return [

    /**
     * Sequence generator.
     */
    'resolver' => [
        'class' => '\Imdgr886\Snowflake\Resolvers\RedisResolver',
    ],

    /**
     * Data center
     */
    'datacenter' => env('DATA_CENTER', 0),

    /**
     * Data center bit length
     */
    'datacenter_length' => 2,

    /**
     * Sequence center bit length
     */
    'sequence_length' => 10,

    'start_time' => '2021-11-19 00:00:00',
];
