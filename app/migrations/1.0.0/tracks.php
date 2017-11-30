<?php

use Phalcon\Db\Column as Column;
use Phalcon\Db\Index as Index;
use Phalcon\Db\Reference as Reference;
use Phalcon\Mvc\Model\Migration;

class TracksMigration_100 extends Migration
{
    public function up()
    {
        $this->morphTable(
            'tracks',
            [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'size'          => 10,
                            'unsigned'      => true,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'first'         => true,
                        ]
                    ),
                    new Column(
                        'service',
                        [
                            'type'     => Column::TYPE_VARCHAR,
                            'size'     => 255,
                            'notNull'  => true,
                            'after'    => 'id',
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                            'after'   => 'service',
                        ]
                    ),
                    new Column(
                        'download_link',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                            'after'   => 'service',
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'PRIMARY',
                        [
                            'id',
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE'      => 'BASE TABLE',
                    'ENGINE'          => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci',
                ],
            ]
        );
    }
}