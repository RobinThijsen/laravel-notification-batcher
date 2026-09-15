<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batch Directory Path
    |--------------------------------------------------------------------------
    |
    | This value is the path where batch files will be stored. You can customize
    | this path according to your application's requirements.
    |
    */
    'directory_path' => 'app/Batches',

    /*
    |--------------------------------------------------------------------------
    | Stub Path
    |--------------------------------------------------------------------------
    |
    | This value is the path to the stub file used for generating batch files.
    | You can customize this path according to your application's requirements.
    |
    */
    'stub_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Queue Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of the queue that will be used for processing batch jobs.
    | You can customize this name according to your application's requirements.
    |
    */
    'queue' => 'batch',

    /*
    |--------------------------------------------------------------------------
    | Queue Delay
    |--------------------------------------------------------------------------
    |
    | This value is the default delay time for processing batch jobs. You can customize
    | this value according to your application's requirements.
    |
    | You can still defined custom one when calling notifyBatch
    |
    */
    'delay' => \Carbon\CarbonInterval::minutes(30),

    /*
    |--------------------------------------------------------------------------
    | Delete After Processed
    |--------------------------------------------------------------------------
    |
    | This value determines whether notification batcher records should be deleted
    | after they have been processed. You can customize this value according to your
    | application's requirements.
    |
    */
    'delete_after_processed' => true,
];
